<?php

namespace App\Http\Controllers;

use App\Models\MediaFile;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class BunnyWebhookController extends Controller
{
    private const STATUS_QUEUED = 0;
    private const STATUS_PROCESSING = 1;
    private const STATUS_ENCODING = 2;
    private const STATUS_FINISHED = 3;
    private const STATUS_RESOLUTION_FINISHED = 4;
    private const STATUS_FAILED = 5;

    public function handle(Request $request): JsonResponse
    {
        $rawBody = $request->getContent();

        $signature = $request->header(
            'X-BunnyStream-Signature'
        );

        $signatureVersion = $request->header(
            'X-BunnyStream-Signature-Version'
        );

        $signatureAlgorithm = $request->header(
            'X-BunnyStream-Signature-Algorithm'
        );

        /*
        |--------------------------------------------------------------------------
        | Signature headers
        |--------------------------------------------------------------------------
        */
        if (
            $signatureVersion !== 'v1' ||
            $signatureAlgorithm !== 'hmac-sha256' ||
            !is_string($signature) ||
            $signature === ''
        ) {
            return response()->json([
                'message' =>
                    'Invalid webhook signature headers.',
            ], 401);
        }

        /*
        |--------------------------------------------------------------------------
        | Secret
        |--------------------------------------------------------------------------
        */
        $secret = config(
            'services.bunny.webhook_secret'
        );

        if (
            !is_string($secret) ||
            $secret === ''
        ) {
            Log::critical(
                'Bunny webhook secret is not configured.'
            );

            return response()->json([
                'message' =>
                    'Webhook configuration error.',
            ], 500);
        }

        /*
        |--------------------------------------------------------------------------
        | HMAC verification
        |--------------------------------------------------------------------------
        */
        $expectedSignature =
            hash_hmac(
                'sha256',
                $rawBody,
                $secret
            );

        if (
            strlen($signature) !==
            strlen($expectedSignature) ||
            !hash_equals(
                $expectedSignature,
                $signature
            )
        ) {
            Log::warning(
                'Invalid Bunny webhook signature.'
            );

            return response()->json([
                'message' =>
                    'Invalid signature.',
            ], 401);
        }

        /*
        |--------------------------------------------------------------------------
        | Decode JSON
        |--------------------------------------------------------------------------
        */
        $payload = json_decode(
            $rawBody,
            true
        );

        if (!is_array($payload)) {
            Log::warning(
                'Invalid Bunny webhook payload.'
            );

            return response()->json([
                'message' =>
                    'Invalid payload.',
            ], 400);
        }

        /*
        |--------------------------------------------------------------------------
        | Extract fields
        |--------------------------------------------------------------------------
        */
        $libraryId = (int) (
            $payload['VideoLibraryId'] ?? 0
        );

        $videoId =
            $payload['VideoGuid'] ?? null;

        $status = isset(
            $payload['Status']
        )
            ? (int) $payload['Status']
            : null;

        if (
            $libraryId <= 0 ||
            !is_string($videoId) ||
            $videoId === '' ||
            $status === null
        ) {
            Log::warning(
                'Incomplete Bunny webhook payload.',
                [
                    'library_id' => $libraryId,
                    'video_id' => $videoId,
                    'status' => $status,
                ]
            );

            return response()->json([
                'message' =>
                    'Invalid webhook payload.',
            ], 400);
        }

        /*
        |--------------------------------------------------------------------------
        | Verify library
        |--------------------------------------------------------------------------
        */
        $expectedLibraryId = (int) config(
            'services.bunny.library_id'
        );

        if (
            $libraryId !==
            $expectedLibraryId
        ) {
            Log::warning(
                'Bunny webhook library mismatch.',
                [
                    'received_library_id' =>
                        $libraryId,

                    'expected_library_id' =>
                        $expectedLibraryId,
                ]
            );

            return response()->json([
                'message' =>
                    'Invalid video library.',
            ], 403);
        }

        /*
        |--------------------------------------------------------------------------
        | Find local reel
        |--------------------------------------------------------------------------
        */
        $mediaFile =
            MediaFile::where(
                'bunny_video_id',
                $videoId
            )
                ->whereHas(
                    'directory',
                    function ($query) {
                        $query->where(
                            'type',
                            'reel'
                        );
                    }
                )
                ->first();

        /*
        |--------------------------------------------------------------------------
        | Unknown Bunny video
        |--------------------------------------------------------------------------
        */
        if (!$mediaFile) {

            Log::info(
                'Bunny webhook received for unknown video.',
                [
                    'video_id' =>
                        $videoId,
                ]
            );

            return response()->json([
                'message' =>
                    'Webhook received.',
            ]);
        }

        /*
        |--------------------------------------------------------------------------
        | State update
        |--------------------------------------------------------------------------
        */
        switch ($status) {

            case self::STATUS_FINISHED:
            case self::STATUS_RESOLUTION_FINISHED:

                $mediaFile->update([
                    'bunny_status' =>
                        'ready',
                ]);

                break;

            case self::STATUS_FAILED:

                if (
                    $mediaFile->bunny_status !==
                    'ready'
                ) {
                    $mediaFile->update([
                        'bunny_status' =>
                            'failed',
                    ]);
                }

                break;

            case self::STATUS_QUEUED:
            case self::STATUS_PROCESSING:
            case self::STATUS_ENCODING:

                /*
                |--------------------------------------------------------------------------
                | Never downgrade terminal states
                |--------------------------------------------------------------------------
                */
                if (
                    !in_array(
                        $mediaFile->bunny_status,
                        [
                            'ready',
                            'failed',
                        ],
                        true
                    )
                ) {
                    $mediaFile->update([
                        'bunny_status' =>
                            'processing',
                    ]);
                }

                break;

            default:

                Log::warning(
                    'Unknown Bunny webhook status.',
                    [
                        'media_file_id' =>
                            $mediaFile->id,

                        'bunny_video_id' =>
                            $videoId,

                        'status' =>
                            $status,
                    ]
                );

                break;
        }

        Log::info(
            'Bunny webhook processed.',
            [
                'media_file_id' =>
                    $mediaFile->id,

                'bunny_video_id' =>
                    $videoId,

                'bunny_status_code' =>
                    $status,

                'local_status' =>
                    $mediaFile
                        ->fresh()
                        ->bunny_status,
            ]
        );

        return response()->json([
            'message' =>
                'Webhook processed successfully.',
        ]);
    }
}