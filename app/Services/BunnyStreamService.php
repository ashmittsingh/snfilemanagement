<?php

namespace App\Services;

use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use RuntimeException;

class BunnyStreamService
{
    private string $host;
    private string $libraryId;
    private string $apiKey;
    private string $cdnHostname;
    private string $embedBase;

    public function __construct()
    {
        $this->host = rtrim(
            (string) config('services.bunny.host'),
            '/'
        );

        $this->libraryId = trim(
            (string) config('services.bunny.library_id')
        );

        $this->apiKey = trim(
            (string) config('services.bunny.api_key')
        );

        $this->cdnHostname = rtrim(
            (string) config('services.bunny.cdn_hostname'),
            '/'
        );

        $this->embedBase = rtrim(
            (string) config('services.bunny.embed'),
            '/'
        );

        if (
            $this->host === '' ||
            $this->libraryId === '' ||
            $this->apiKey === '' ||
            $this->cdnHostname === '' ||
            $this->embedBase === ''
        ) {
            throw new RuntimeException(
                'Bunny Stream configuration is incomplete.'
            );
        }
    }

    /**
     * Create an empty Bunny video object.
     */
    public function createVideo(string $title): string
    {
        $response = Http::timeout(60)
            ->connectTimeout(15)
            ->withHeaders([
                'AccessKey' => $this->apiKey,
                'Accept' => 'application/json',
                'Content-Type' => 'application/json',
            ])
            ->post(
                "{$this->host}/library/{$this->libraryId}/videos",
                [
                    'title' => $title,
                ]
            );

        if (!$response->successful()) {
            $this->logHttpError(
                'Bunny create video failed',
                $response,
                [
                    'title' => $title,
                ]
            );

            throw new RuntimeException(
                'Unable to create Bunny video.'
            );
        }

        $videoId = $response->json('guid');

        if (
            !is_string($videoId) ||
            $videoId === ''
        ) {
            Log::error(
                'Bunny video ID missing from create response.'
            );

            throw new RuntimeException(
                'Bunny video ID was not returned.'
            );
        }

        return $videoId;
    }

    /**
     * Upload binary video source.
     *
     * Do not add blind HTTP retries here.
     * Queue/job-level recovery handles failures.
     */
    public function uploadVideo(
        string $filePath,
        string $videoId
    ): void {
        if (!is_file($filePath)) {
            throw new RuntimeException(
                'Video file not found.'
            );
        }

        $stream = fopen(
            $filePath,
            'rb'
        );

        if ($stream === false) {
            throw new RuntimeException(
                'Unable to open video file.'
            );
        }

        try {
            $response = Http::timeout(600)
                ->connectTimeout(60)
                ->withHeaders([
                    'AccessKey' => $this->apiKey,
                    'Content-Type' =>
                        'application/octet-stream',
                ])
                ->withBody(
                    $stream,
                    'application/octet-stream'
                )
                ->put(
                    "{$this->host}/library/{$this->libraryId}/videos/{$videoId}"
                );

            if (!$response->successful()) {
                $this->logHttpError(
                    'Bunny video upload failed',
                    $response,
                    [
                        'video_id' => $videoId,
                    ]
                );

                throw new RuntimeException(
                    'Bunny video upload failed.'
                );
            }

        } finally {
            fclose($stream);
        }
    }

    /**
     * Get remote Bunny video state.
     *
     * null = video does not exist.
     */
    public function getVideo(
        string $videoId
    ): ?array {
        $response = Http::timeout(60)
            ->connectTimeout(15)
            ->withHeaders([
                'AccessKey' => $this->apiKey,
                'Accept' => 'application/json',
            ])
            ->get(
                "{$this->host}/library/{$this->libraryId}/videos/{$videoId}"
            );

        if ($response->status() === 404) {
            return null;
        }

        if (!$response->successful()) {
            $this->logHttpError(
                'Bunny get video failed',
                $response,
                [
                    'video_id' => $videoId,
                ]
            );

            throw new RuntimeException(
                'Unable to fetch Bunny video.'
            );
        }

        $data = $response->json();

        if (!is_array($data)) {
            throw new RuntimeException(
                'Invalid Bunny video response.'
            );
        }

        return $data;
    }

    /**
     * Check whether Bunny has received the source video.
     *
     * A processing status alone is not enough for recovery because
     * an empty video object can exist before the binary upload.
     */
    public function hasUploadedSource(
        array $video
    ): bool {
        if (
            !empty($video['hasOriginal'])
        ) {
            return true;
        }

        return (
            ((int) ($video['storageSize'] ?? 0) > 0) ||
            ((float) ($video['length'] ?? 0) > 0)
        );
    }

    /**
     * Generate playback URLs.
     */
    public function getPlaybackUrls(
        string $videoId
    ): array {
        return [
            'play_url' =>
                "https://{$this->cdnHostname}/{$videoId}/playlist.m3u8",

            'embed_url' =>
                "{$this->embedBase}/{$this->libraryId}/{$videoId}",
        ];
    }

    /**
     * Delete Bunny video.
     *
     * 404 is treated as success because the remote resource
     * is already gone.
     */
    public function deleteVideo(
        string $videoId
    ): bool {
        try {
            $response = Http::timeout(60)
                ->connectTimeout(15)
                ->withHeaders([
                    'AccessKey' => $this->apiKey,
                ])
                ->delete(
                    "{$this->host}/library/{$this->libraryId}/videos/{$videoId}"
                );

            if ($response->status() === 404) {
                return true;
            }

            if (!$response->successful()) {
                $this->logHttpError(
                    'Bunny delete video failed',
                    $response,
                    [
                        'video_id' => $videoId,
                    ]
                );

                return false;
            }

            return true;

        } catch (\Throwable $e) {
            Log::error(
                'Bunny delete video exception.',
                [
                    'video_id' => $videoId,
                    'message' => $e->getMessage(),
                ]
            );

            return false;
        }
    }

    /**
     * Log only safe HTTP metadata.
     * Never log Bunny response body because it is unnecessary
     * and can expose remote response details.
     */
    private function logHttpError(
        string $message,
        Response $response,
        array $context = []
    ): void {
        Log::error(
            $message,
            array_merge(
                $context,
                [
                    'status' =>
                        $response->status(),
                ]
            )
        );
    }
}