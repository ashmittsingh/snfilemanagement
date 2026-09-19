<?php

namespace App\Jobs;

use App\Models\MediaFile;
use App\Services\BunnyStorageService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use RuntimeException;
use Throwable;

class UploadMediaToBunny implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    /**
     * Maximum attempts for transient failures.
     */
    public int $tries = 3;

    /**
     * Maximum execution time for a single job.
     */
    public int $timeout = 900;
public bool $failOnTimeout = true;
    /**
     * Retry delays in seconds.
     */
    public array $backoff = [10, 30, 60];

    public function __construct(
        public int $mediaFileId
    ) {
    }

    public function handle(BunnyStorageService $bunnyStorage): void
    {
        $mediaFile = MediaFile::with('directory')
    ->find($this->mediaFileId);

        if (!$mediaFile) {
            Log::warning('Media file not found for Bunny Storage job.', [
                'media_file_id' => $this->mediaFileId,
            ]);

            return;
        }

        $mediaDirectory = $mediaFile->directory;

        if (!$mediaDirectory) {
            $this->markFailed(
                $mediaFile,
                'Media directory not found.'
            );

            return;
        }

        /*
         * This job is exclusively for Image and Document.
         * Reels continue using Bunny Stream.
         */
        if (!in_array(
            $mediaDirectory->type,
            ['image', 'document'],
            true
        )) {
            Log::warning(
                'Invalid media type for Bunny Storage job.',
                [
                    'media_file_id' => $mediaFile->id,
                    'media_type' => $mediaDirectory->type,
                ]
            );

            return;
        }

        /*
         * Idempotency:
         * If the file is already successfully stored, do nothing.
         */
        if (
            $mediaFile->bunny_status === 'ready' &&
            filled($mediaFile->storage_path)
        ) {
            return;
        }

        /*
         * Generate the remote path only once.
         *
         * On retry, the existing storage_path is reused.
         */
        if (blank($mediaFile->storage_path)) {
            $mediaFile->storage_path = $this->generateStoragePath(
                $mediaFile,
                $mediaDirectory->type
            );

            $mediaFile->save();
        }

        /*
         * The local source must remain available until
         * remote upload and DB persistence are complete.
         */
        if (
            blank($mediaFile->file_path) ||
            !Storage::disk('local')->exists($mediaFile->file_path)
        ) {
            /*
             * The upload may have succeeded on a previous attempt
             * before the DB was fully updated.
             *
             * We have a stable storage_path, so we can safely
             * finalize the record using the deterministic CDN URL.
             */
            if (filled($mediaFile->storage_path)) {
                $this->finalizeExistingRemoteFile(
                    $mediaFile,
                    $bunnyStorage
                );

                return;
            }

            $this->markFailed(
                $mediaFile,
                'Local source file is missing.'
            );

            return;
        }

        $mediaFile->bunny_status = 'uploading';
        $mediaFile->save();

        $localPath = Storage::disk('local')
            ->path($mediaFile->file_path);

        try {
            $result = $bunnyStorage->upload(
                $localPath,
                $mediaFile->storage_path
            );

            /*
             * Remote upload succeeded.
             *
             * Persist remote information BEFORE deleting
             * the local source.
             */
            $mediaFile->storage_path = $result['storage_path'];
            $mediaFile->storage_url = $result['storage_url'];
            $mediaFile->bunny_status = 'ready';
            $mediaFile->save();

            /*
             * Local cleanup happens only after successful
             * Bunny upload + successful DB update.
             */
            $this->deleteLocalSource($mediaFile);

            Log::info(
                'Media uploaded to Bunny Storage successfully.',
                [
                    'media_file_id' => $mediaFile->id,
                    'media_type' => $mediaDirectory->type,
                ]
            );
        } catch (Throwable $exception) {
            /*
             * NEVER remove the local source on failure.
             * Queue retries need the source file.
             */
            $this->markFailed(
                $mediaFile,
                $exception->getMessage()
            );

            throw $exception;
        }
    }

    /**
     * Generate a unique and stable Bunny Storage path.
     */
    private function generateStoragePath(
        MediaFile $mediaFile,
        string $type
    ): string {
        $extension = strtolower(
            pathinfo(
                $mediaFile->file_name,
                PATHINFO_EXTENSION
            )
        );

        $filename = Str::uuid()->toString();

        return sprintf(
            '%s/%d/%s%s',
            $type,
            $mediaFile->media_directory_id,
            $filename,
            $extension !== '' ? '.' . $extension : ''
        );
    }

    /**
     * Finalize a record when the remote upload may already exist.
     */
    private function finalizeExistingRemoteFile(
        MediaFile $mediaFile,
        BunnyStorageService $bunnyStorage
    ): void {
        $mediaFile->storage_url = $bunnyStorage->getUrl(
            $mediaFile->storage_path
        );

        $mediaFile->bunny_status = 'ready';
        $mediaFile->save();

        $this->deleteLocalSource($mediaFile);

        Log::info(
            'Media record finalized using existing Bunny Storage path.',
            [
                'media_file_id' => $mediaFile->id,
            ]
        );
    }

    /**
     * Remove the temporary local source after successful completion.
     */
    private function deleteLocalSource(MediaFile $mediaFile): void
    {
        if (blank($mediaFile->file_path)) {
            return;
        }

        $disk = Storage::disk('local');

        if ($disk->exists($mediaFile->file_path)) {
            $disk->delete($mediaFile->file_path);
        }

        $mediaFile->file_path = null;
        $mediaFile->save();
    }

    /**
     * Mark the media file as failed.
     */
    private function markFailed(
        MediaFile $mediaFile,
        string $message
    ): void {
        $mediaFile->bunny_status = 'failed';
        $mediaFile->save();

        Log::error('Bunny Storage media upload failed.', [
            'media_file_id' => $mediaFile->id,
            'attempt' => $this->attempts(),
            'message' => $message,
        ]);
    }

    /**
     * Called after all queue attempts have failed.
     */
    public function failed(Throwable $exception): void
    {
        $mediaFile = MediaFile::find($this->mediaFileId);

        if (!$mediaFile) {
            return;
        }

        /*
         * Keep the source file intact so the admin can retry
         * the same MediaFile later.
         */
        $mediaFile->bunny_status = 'failed';
        $mediaFile->save();

        Log::error(
            'Bunny Storage job permanently failed.',
            [
                'media_file_id' => $mediaFile->id,
                'message' => $exception->getMessage(),
            ]
        );
    }
}