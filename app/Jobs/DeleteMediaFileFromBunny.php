<?php

namespace App\Jobs;

use App\Services\BunnyStorageService;
use App\Services\BunnyStreamService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use RuntimeException;
use Throwable;

class DeleteMediaFileFromBunny implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public int $tries = 3;

    public int $timeout = 180;

    public bool $failOnTimeout = true;

    public array $backoff = [10, 30, 60];

    public function __construct(
        public int $mediaFileId,
        public string $type,
        public ?string $storagePath = null,
        public ?string $bunnyVideoId = null
    ) {
    }

    public function handle(
        BunnyStorageService $bunnyStorage,
        BunnyStreamService $bunnyStream
    ): void {

        if (
            $this->type === 'reel' &&
            filled($this->bunnyVideoId)
        ) {

            if (
                !$bunnyStream->deleteVideo(
                    $this->bunnyVideoId
                )
            ) {
                throw new RuntimeException(
                    'Unable to delete Bunny video.'
                );
            }

            Log::info(
                'Bunny Stream media deleted.',
                [
                    'media_file_id' =>
                        $this->mediaFileId,

                    'bunny_video_id' =>
                        $this->bunnyVideoId,
                ]
            );

            return;
        }

        if (
            in_array(
                $this->type,
                ['image', 'document'],
                true
            ) &&
            filled($this->storagePath)
        ) {

            if (
                !$bunnyStorage->delete(
                    $this->storagePath
                )
            ) {
                throw new RuntimeException(
                    'Unable to delete Bunny Storage file.'
                );
            }

            Log::info(
                'Bunny Storage media deleted.',
                [
                    'media_file_id' =>
                        $this->mediaFileId,

                    'storage_path' =>
                        $this->storagePath,
                ]
            );

            return;
        }

        Log::warning(
            'No Bunny resource found for deletion.',
            [
                'media_file_id' =>
                    $this->mediaFileId,
            ]
        );
    }

    public function failed(
        Throwable $exception
    ): void {

        Log::error(
            'Bunny media deletion permanently failed.',
            [
                'media_file_id' =>
                    $this->mediaFileId,

                'type' =>
                    $this->type,

                'storage_path' =>
                    $this->storagePath,

                'bunny_video_id' =>
                    $this->bunnyVideoId,

                'message' =>
                    $exception->getMessage(),
            ]
        );
    }
}