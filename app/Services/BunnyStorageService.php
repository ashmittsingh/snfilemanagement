<?php

namespace App\Services;

use Exception;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use RuntimeException;

class BunnyStorageService
{
    private const DEFAULT_UPLOAD_TIMEOUT = 900;
    private const DEFAULT_CONNECT_TIMEOUT = 30;
    private const DELETE_TIMEOUT = 120;

    private string $host;
    private string $zone;
    private string $apiKey;
    private string $cdnHostname;

    public function __construct()
    {
        $this->host = rtrim(
            (string) config('services.bunny.storage.host'),
            '/'
        );

        $this->zone = trim(
            (string) config('services.bunny.storage.zone')
        );

        $this->apiKey = trim(
            (string) config('services.bunny.storage.api_key')
        );

        $this->cdnHostname = rtrim(
            (string) config('services.bunny.storage.cdn_hostname'),
            '/'
        );
    }

    /**
     * Upload a local file to Bunny Storage.
     *
     * @throws Exception
     */
    public function upload(
        string $localPath,
        string $storagePath
    ): array {
        $this->validateConfiguration();

        if (!is_file($localPath) || !is_readable($localPath)) {
            throw new RuntimeException(
                'The local file does not exist or is not readable.'
            );
        }

        $storagePath = $this->normalizeStoragePath($storagePath);

        if ($storagePath === '') {
            throw new RuntimeException(
                'Bunny Storage path cannot be empty.'
            );
        }

        try {
            $stream = fopen($localPath, 'rb');

            if ($stream === false) {
                throw new RuntimeException(
                    'Unable to open the local file for reading.'
                );
            }

            try {
                $response = Http::withHeaders([
                    'AccessKey' => $this->apiKey,
                    'Content-Type' => 'application/octet-stream',
                ])
                    ->connectTimeout(self::DEFAULT_CONNECT_TIMEOUT)
                    ->timeout(self::DEFAULT_UPLOAD_TIMEOUT)
                    ->withBody(
                        $stream,
                        'application/octet-stream'
                    )
                    ->put(
                        $this->buildStorageUrl($storagePath)
                    );
            } finally {
                fclose($stream);
            }

            $this->ensureSuccessfulResponse(
                $response,
                'upload',
                $storagePath
            );

            return [
                'storage_path' => $storagePath,
                'storage_url' => $this->getUrl($storagePath),
            ];
        } catch (Exception $e) {
            Log::error('Bunny Storage upload failed.', [
                'storage_path' => $storagePath,
                'exception' => $e::class,
                'message' => $e->getMessage(),
            ]);

            throw $e;
        }
    }

    /**
     * Generate the public CDN URL for a stored file.
     *
     * This method does not make a network request.
     */
    public function getUrl(string $storagePath): string
    {
        if ($this->cdnHostname === '') {
            throw new RuntimeException(
                'Bunny Storage CDN hostname is not configured.'
            );
        }

        $storagePath = $this->normalizeStoragePath($storagePath);

        if ($storagePath === '') {
            throw new RuntimeException(
                'Bunny Storage path cannot be empty.'
            );
        }

        return $this->cdnHostname . '/' . $storagePath;
    }

    /**
     * Delete a file from Bunny Storage.
     *
     * A 404 is treated as success because the desired final state
     * is that the remote file does not exist.
     */
    public function delete(string $storagePath): bool
    {
        $this->validateConfiguration();

        $storagePath = $this->normalizeStoragePath($storagePath);

        if ($storagePath === '') {
            return true;
        }

        try {
            $response = Http::withHeaders([
                'AccessKey' => $this->apiKey,
            ])
                ->connectTimeout(self::DEFAULT_CONNECT_TIMEOUT)
                ->timeout(self::DELETE_TIMEOUT)
                ->delete(
                    $this->buildStorageUrl($storagePath)
                );

            if (
                $response->successful() ||
                $response->status() === 404
            ) {
                return true;
            }

            Log::error('Bunny Storage delete failed.', [
                'storage_path' => $storagePath,
                'status' => $response->status(),
            ]);

            return false;
        } catch (Exception $e) {
            Log::error('Bunny Storage delete exception.', [
                'storage_path' => $storagePath,
                'exception' => $e::class,
                'message' => $e->getMessage(),
            ]);

            return false;
        }
    }

    /**
     * Build the Bunny Storage API URL.
     */
    private function buildStorageUrl(string $storagePath): string
    {
        return sprintf(
            '%s/%s/%s',
            $this->host,
            rawurlencode($this->zone),
            str_replace(
                '%2F',
                '/',
                rawurlencode($storagePath)
            )
        );
    }

    /**
     * Normalize a relative Bunny Storage path.
     */
    private function normalizeStoragePath(string $storagePath): string
    {
        $storagePath = trim($storagePath);

        return trim($storagePath, '/');
    }

    /**
     * Validate required Bunny Storage configuration.
     */
    private function validateConfiguration(): void
    {
        if ($this->host === '') {
            throw new RuntimeException(
                'Bunny Storage host is not configured.'
            );
        }

        if ($this->zone === '') {
            throw new RuntimeException(
                'Bunny Storage zone is not configured.'
            );
        }

        if ($this->apiKey === '') {
            throw new RuntimeException(
                'Bunny Storage API key is not configured.'
            );
        }
    }

    /**
     * Ensure Bunny returned a successful HTTP response.
     *
     * @throws RuntimeException
     */
    private function ensureSuccessfulResponse(
        Response $response,
        string $operation,
        string $storagePath
    ): void {
        if ($response->successful()) {
            return;
        }

        Log::error("Bunny Storage {$operation} request failed.", [
            'storage_path' => $storagePath,
            'status' => $response->status(),
        ]);

        throw new RuntimeException(
            "Bunny Storage {$operation} failed with HTTP status {$response->status()}."
        );
    }
}