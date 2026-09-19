<?php

namespace App\Jobs;

use App\Models\MediaFile;
use App\Services\BunnyStreamService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use RuntimeException;
use Throwable;

class UploadReelToBunny implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public int $tries = 3;

    public int $timeout = 900;

    public bool $failOnTimeout = true;

    public array $backoff = [10, 30, 60];

    public function __construct(
        public int $mediaFileId
    ) {
    }

    public function handle(
        BunnyStreamService $bunny
    ): void {
        $mediaFile = MediaFile::find(
            $this->mediaFileId
        );

        if (!$mediaFile) {
            return;
        }

        /*
        |--------------------------------------------------------------------------
        | Already complete locally
        |--------------------------------------------------------------------------
        */
        if (
            $mediaFile->bunny_video_id &&
            $mediaFile->bunny_status === 'ready'
        ) {
            return;
        }

        /*
        |--------------------------------------------------------------------------
        | Recover existing remote Bunny video
        |--------------------------------------------------------------------------
        */
        if ($mediaFile->bunny_video_id) {

            $videoId =
                $mediaFile->bunny_video_id;

            $remoteVideo =
                $bunny->getVideo($videoId);

            /*
            |--------------------------------------------------------------------------
            | Remote video missing
            |--------------------------------------------------------------------------
            */
            if ($remoteVideo === null) {

                $mediaFile->update([
                    'bunny_video_id' => null,
                    'bunny_play_url' => null,
                    'bunny_embed_url' => null,
                    'bunny_status' => 'pending',
                ]);

            } else {

                $remoteStatus = (int) (
                    $remoteVideo['status'] ?? -1
                );

                /*
                |--------------------------------------------------------------------------
                | Remote video is finished
                |--------------------------------------------------------------------------
                */
                if (
                    in_array(
                        $remoteStatus,
                        [3, 4],
                        true
                    )
                ) {
                    $this->markReady(
                        $mediaFile,
                        $bunny,
                        $videoId
                    );

                    return;
                }

                /*
                |--------------------------------------------------------------------------
                | Active remote processing
                |--------------------------------------------------------------------------
                |
                | IMPORTANT:
                | Status alone is not considered proof that
                | binary upload completed.
                |
                */
                if (
                    in_array(
                        $remoteStatus,
                        [0, 1, 2],
                        true
                    )
                ) {

                    if (
                        $bunny->hasUploadedSource(
                            $remoteVideo
                        )
                    ) {
                        $this->markProcessing(
                            $mediaFile,
                            $bunny,
                            $videoId
                        );

                        return;
                    }

                    /*
                    |--------------------------------------------------------------------------
                    | Bunny object exists but source was never uploaded.
                    | Continue below and upload to the SAME video ID.
                    |--------------------------------------------------------------------------
                    */
                    $mediaFile->update([
                        'bunny_status' => 'uploading',
                    ]);
                }

                /*
                |--------------------------------------------------------------------------
                | Remote processing failed
                |--------------------------------------------------------------------------
                */
                if (
                    $remoteStatus === 5
                ) {

                    if (
                        !$bunny->deleteVideo(
                            $videoId
                        )
                    ) {
                        throw new RuntimeException(
                            'Unable to clean failed Bunny video.'
                        );
                    }

                    $mediaFile->update([
                        'bunny_video_id' => null,
                        'bunny_play_url' => null,
                        'bunny_embed_url' => null,
                        'bunny_status' => 'pending',
                    ]);
                }
            }
        }

        /*
        |--------------------------------------------------------------------------
        | Local source is required for upload
        |--------------------------------------------------------------------------
        */
        if (
            !$mediaFile->file_path ||
            !Storage::disk('local')->exists(
                $mediaFile->file_path
            )
        ) {
            throw new RuntimeException(
                'Reel source file is missing.'
            );
        }

        $absolutePath =
            Storage::disk('local')->path(
                $mediaFile->file_path
            );

        /*
        |--------------------------------------------------------------------------
        | Create Bunny video only if no remote ID exists
        |--------------------------------------------------------------------------
        */
        $videoId =
            $mediaFile->bunny_video_id;

        if (!$videoId) {

            $videoId = $bunny->createVideo(
                $mediaFile->file_name
            );

            /*
            |--------------------------------------------------------------------------
            | Persist remote ID immediately.
            |--------------------------------------------------------------------------
            */
            $mediaFile->update([
                'bunny_video_id' => $videoId,
                'bunny_status' => 'uploading',
            ]);

        } else {

            $mediaFile->update([
                'bunny_status' => 'uploading',
            ]);
        }

        /*
        |--------------------------------------------------------------------------
        | Upload source
        |--------------------------------------------------------------------------
        */
        $bunny->uploadVideo(
            $absolutePath,
            $videoId
        );

        /*
        |--------------------------------------------------------------------------
        | Save playback URLs
        |--------------------------------------------------------------------------
        */
        $urls =
            $bunny->getPlaybackUrls(
                $videoId
            );

        $mediaFile->update([
            'bunny_play_url' =>
                $urls['play_url'],

            'bunny_embed_url' =>
                $urls['embed_url'],

            'bunny_status' =>
                'processing',
        ]);

        /*
        |--------------------------------------------------------------------------
        | Bunny has accepted the source.
        | Local temporary source is no longer required.
        |--------------------------------------------------------------------------
        */
        $this->deleteLocalSource(
            $mediaFile
        );
    }

    /**
     * Mark Bunny video ready.
     */
    private function markReady(
        MediaFile $mediaFile,
        BunnyStreamService $bunny,
        string $videoId
    ): void {
        $urls =
            $bunny->getPlaybackUrls(
                $videoId
            );

        $mediaFile->update([
            'bunny_play_url' =>
                $urls['play_url'],

            'bunny_embed_url' =>
                $urls['embed_url'],

            'bunny_status' =>
                'ready',
        ]);

        $this->deleteLocalSource(
            $mediaFile
        );
    }

    /**
     * Mark Bunny video as processing.
     */
    private function markProcessing(
        MediaFile $mediaFile,
        BunnyStreamService $bunny,
        string $videoId
    ): void {
        $urls =
            $bunny->getPlaybackUrls(
                $videoId
            );

        $mediaFile->update([
            'bunny_play_url' =>
                $urls['play_url'],

            'bunny_embed_url' =>
                $urls['embed_url'],

            'bunny_status' =>
                'processing',
        ]);

        $this->deleteLocalSource(
            $mediaFile
        );
    }

    /**
     * Delete local temporary source.
     */
    private function deleteLocalSource(
        MediaFile $mediaFile
    ): void {
        if (!$mediaFile->file_path) {
            return;
        }

        $path =
            $mediaFile->file_path;

        if (
            Storage::disk('local')->exists(
                $path
            )
        ) {
            Storage::disk('local')->delete(
                $path
            );
        }

        $mediaFile->update([
            'file_path' => null,
        ]);
    }

    /**
     * Called only after all queue retries are exhausted.
     */
    public function failed(
        Throwable $exception
    ): void {
        $mediaFile =
            MediaFile::find(
                $this->mediaFileId
            );

        if (!$mediaFile) {
            return;
        }

        /*
        |--------------------------------------------------------------------------
        | Never overwrite a successful state.
        |--------------------------------------------------------------------------
        */
        if (
            $mediaFile->bunny_status === 'ready'
        ) {
            return;
        }

        /*
        |--------------------------------------------------------------------------
        | Last remote-state check.
        |--------------------------------------------------------------------------
        |
        | A queue failure can happen after Bunny accepted the upload.
        | Do not blindly mark failed without checking remote state.
        |
        */
        try {

            if (
                $mediaFile->bunny_video_id
            ) {

                $bunny =
                    app(BunnyStreamService::class);

                $remoteVideo =
                    $bunny->getVideo(
                        $mediaFile->bunny_video_id
                    );

                if ($remoteVideo) {

                    $remoteStatus =
                        (int) (
                            $remoteVideo['status'] ?? -1
                        );

                    if (
                        in_array(
                            $remoteStatus,
                            [3, 4],
                            true
                        )
                    ) {
                        $this->markReady(
                            $mediaFile,
                            $bunny,
                            $mediaFile->bunny_video_id
                        );

                        return;
                    }

                    if (
                        in_array(
                            $remoteStatus,
                            [0, 1, 2],
                            true
                        ) &&
                        $bunny->hasUploadedSource(
                            $remoteVideo
                        )
                    ) {
                        $this->markProcessing(
                            $mediaFile,
                            $bunny,
                            $mediaFile->bunny_video_id
                        );

                        return;
                    }
                }
            }

        } catch (Throwable $remoteException) {

            Log::warning(
                'Unable to verify Bunny state after final job failure.',
                [
                    'media_file_id' =>
                        $mediaFile->id,

                    'bunny_video_id' =>
                        $mediaFile->bunny_video_id,

                    'message' =>
                        $remoteException->getMessage(),
                ]
            );
        }

        $mediaFile->update([
            'bunny_status' => 'failed',
        ]);

        Log::error(
            'Bunny reel upload permanently failed.',
            [
                'media_file_id' =>
                    $mediaFile->id,

                'bunny_video_id' =>
                    $mediaFile->bunny_video_id,

                'message' =>
                    $exception->getMessage(),
            ]
        );
    }
}