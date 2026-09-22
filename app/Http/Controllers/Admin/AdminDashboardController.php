<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Jobs\DeleteMediaFileFromBunny;
use App\Models\MediaDirectory;
use App\Models\MediaFile;
use App\Services\BunnyStreamService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Throwable;
use Illuminate\View\View;

class AdminDashboardController extends Controller
{
    public function index(Request $request)
    {
        $pageTitle = 'Admin Files';

        $search = trim(
            (string) $request->input('search')
        );

        $type = $request->input('type');

        $types = MediaDirectory::query()
            ->whereNotNull('type')
            ->where('type', '!=', '')
            ->distinct()
            ->orderBy('type')
            ->pluck('type');

        if ($type !== null && !$types->contains($type)) {
            $type = null;
        }

        $directories = MediaDirectory::query()
            ->withCount('files')

            ->withCount([
                'files as processing_files_count' => function ($query) {
                    $query->whereIn(
                        'bunny_status',
                        [
                            'pending',
                            'uploading',
                            'processing',
                        ]
                    );
                },
            ])

            ->withCount([
                'files as ready_files_count' => function ($query) {
                    $query->where(
                        'bunny_status',
                        'ready'
                    );
                },
            ])

            ->withCount([
                'files as failed_files_count' => function ($query) {
                    $query->where(
                        'bunny_status',
                        'failed'
                    );
                },
            ])

            ->when(
                $search !== '',
                function ($query) use ($search) {
                    $query->where(function ($q) use ($search) {
                        $q->where(
                            'name',
                            'like',
                            "%{$search}%"
                        )->orWhere(
                                'description',
                                'like',
                                "%{$search}%"
                            );
                    });
                }
            )

            ->when(
                $type,
                function ($query) use ($type) {
                    $query->where(
                        'type',
                        $type
                    );
                }
            )

            ->latest('id')
            ->paginate(12)
            ->withQueryString();

        $totalDirectories = MediaDirectory::count();

        $totalImages = MediaFile::whereHas(
            'directory',
            function ($query) {
                $query->where('type', 'image');
            }
        )->count();

        $totalReels = MediaFile::whereHas(
            'directory',
            function ($query) {
                $query->where('type', 'reel');
            }
        )->count();

        $totalDocuments = MediaFile::whereHas(
            'directory',
            function ($query) {
                $query->where('type', 'document');
            }
        )->count();

        if ($request->ajax()) {
            return response()->json([
                'html' => view(
                    'admin.files.partials',
                    compact(
                        'directories',
                        'search',
                        'type'
                    )
                )->render(),

                'pagination' => $directories->links()->render(),
            ]);
        }

        return view(
            'admin.files.list',
            compact(
                'directories',
                'totalDirectories',
                'totalImages',
                'totalReels',
                'totalDocuments',
                'search',
                'type',
                'types',
                'pageTitle'
            )
        );
    }


    /**
     * Return live Bunny encode progress for a reel directory.
     */
    public function reelProgress(
        int $id,
        BunnyStreamService $bunny
    ): JsonResponse {
        $directory = MediaDirectory::query()
            ->where('id', $id)
            ->where('type', 'reel')
            ->with([
                'files:id,media_directory_id,bunny_video_id,bunny_status',
            ])
            ->first();

        if (!$directory) {
            return response()->json([
                'status' => false,
                'message' => 'Reel directory not found.',
            ], 404);
        }

        $files = $directory->files;

        if ($files->isEmpty()) {
            return response()->json([
                'status' => true,
                'progress' => 0,
                'processing' => false,
                'ready' => false,
                'failed' => false,
                'status_label' => 'Processing',
            ]);
        }

        $totalFiles = $files->count();

        $totalProgress = 0;

        $hasProcessing = false;
        $hasFailed = false;
        $allReady = true;

        foreach ($files as $file) {

            $fileStatus = (string) $file->bunny_status;

            /*
            |--------------------------------------------------------------------------
            | READY
            |--------------------------------------------------------------------------
            */
            if ($fileStatus === 'ready') {

                $totalProgress += 100;

                continue;
            }

            /*
            |--------------------------------------------------------------------------
            | FAILED
            |--------------------------------------------------------------------------
            */
            if ($fileStatus === 'failed') {

                $hasFailed = true;
                $allReady = false;

                /*
                | Failed file contributes 0%.
                */
                $totalProgress += 0;

                continue;
            }

            /*
            |--------------------------------------------------------------------------
            | PENDING / UPLOADING
            |--------------------------------------------------------------------------
            */
            if (
                in_array(
                    $fileStatus,
                    [
                        'pending',
                        'uploading',
                    ],
                    true
                )
            ) {

                $hasProcessing = true;
                $allReady = false;

                /*
                | Bunny encodeProgress is not available yet.
                */
                $totalProgress += 0;

                continue;
            }

            /*
            |--------------------------------------------------------------------------
            | BUNNY PROCESSING
            |--------------------------------------------------------------------------
            */
            if (
                $fileStatus === 'processing' &&
                $file->bunny_video_id
            ) {

                $hasProcessing = true;
                $allReady = false;

                $video = $bunny->getVideo(
                    $file->bunny_video_id
                );

                if ($video === null) {

                    $totalProgress += 0;

                } else {

                    $encodeProgress = (int) (
                        $video['encodeProgress'] ?? 0
                    );

                    $encodeProgress = max(
                        0,
                        min(
                            100,
                            $encodeProgress
                        )
                    );

                    $totalProgress += $encodeProgress;
                }

                continue;
            }

            /*
            |--------------------------------------------------------------------------
            | Unknown state
            |--------------------------------------------------------------------------
            */
            $hasProcessing = true;
            $allReady = false;

            $totalProgress += 0;
        }

        /*
        |--------------------------------------------------------------------------
        | Overall directory progress
        |--------------------------------------------------------------------------
        */
        $progress = (int) round(
            $totalProgress / $totalFiles
        );

        /*
        |--------------------------------------------------------------------------
        | Final state
        |--------------------------------------------------------------------------
        */
        if ($allReady) {

            $progress = 100;
            $statusLabel = 'Ready';

        } elseif ($hasFailed) {

            $statusLabel = 'Failed';

        } else {

            $statusLabel = 'Processing';
        }

        return response()->json([
            'status' => true,
            'progress' => $progress,
            'processing' => $hasProcessing,
            'ready' => $allReady,
            'failed' => $hasFailed,
            'status_label' => $statusLabel,
        ]);
    }


    /**
     * Delete a media directory and all associated files.
     */
    public function destroy(
        MediaDirectory $directory
    ): JsonResponse {

        /*
        |--------------------------------------------------------------------------
        | Load all files belonging to this directory
        |--------------------------------------------------------------------------
        */

        $files = $directory->files()->get([
            'id',
            'media_directory_id',
            'file_path',
            'storage_path',
            'bunny_video_id',
        ]);

        /*
        |--------------------------------------------------------------------------
        | Capture Bunny deletion data before DB records are removed
        |--------------------------------------------------------------------------
        */

        $deleteJobs = $files
            ->map(function (MediaFile $mediaFile) use ($directory) {
                return [
                    'media_file_id' =>
                        $mediaFile->id,

                    'type' =>
                        $directory->type,

                    'storage_path' =>
                        $mediaFile->storage_path,

                    'bunny_video_id' =>
                        $mediaFile->bunny_video_id,
                ];
            })
            ->values()
            ->all();

        /*
        |--------------------------------------------------------------------------
        | Capture local source paths before deleting DB records
        |--------------------------------------------------------------------------
        */

        $localFilePaths = $files
            ->pluck('file_path')
            ->filter()
            ->values()
            ->all();

        try {

            DB::transaction(function () use ($directory, $files, $deleteJobs, $localFilePaths) {

                /*
                |--------------------------------------------------------------------------
                | Delete temporary local files
                |--------------------------------------------------------------------------
                */

                $disk = Storage::disk('local');

                foreach ($localFilePaths as $filePath) {

                    if ($disk->exists($filePath)) {
                        $disk->delete($filePath);
                    }
                }

                /*
                |--------------------------------------------------------------------------
                | Delete MediaFile records
                |--------------------------------------------------------------------------
                */

                foreach ($files as $mediaFile) {
                    $mediaFile->delete();
                }

                /*
                |--------------------------------------------------------------------------
                | Delete directory
                |--------------------------------------------------------------------------
                */

                $directory->delete();

                /*
                |--------------------------------------------------------------------------
                | Delete Bunny resources after successful DB commit
                |--------------------------------------------------------------------------
                */

                foreach ($deleteJobs as $deleteJob) {

                    DeleteMediaFileFromBunny::dispatch(
                        $deleteJob['media_file_id'],
                        $deleteJob['type'],
                        $deleteJob['storage_path'],
                        $deleteJob['bunny_video_id']
                    )->afterCommit();
                }
            });

            return response()->json([
                'status' => true,
                'message' => 'Media directory deleted successfully.',
                'redirect' => route('admin.dashboard'),
            ]);

        } catch (Throwable $exception) {

            report($exception);

            return response()->json([
                'status' => false,
                'message' =>
                    'Unable to delete media directory. Please try again.',
            ], 500);
        }
    }

    public function view(MediaDirectory $directory)
    {
        $pageTitle = 'Admin Media Details';

        $directory->load('files');

        return view(
            'admin.files.view',
            compact(
                'pageTitle',
                'directory'
            )
        );
    }

    public function download(
        MediaFile $file,
        BunnyStreamService $bunnyStreamService
    ) {
        $file->loadMissing('directory');

        /*
        |--------------------------------------------------------------------------
        | Bunny Stream Reel / Video
        |--------------------------------------------------------------------------
        */
        if ($file->directory?->type === 'reel') {

            if (!$file->bunny_video_id) {
                abort(404, 'Bunny video not found.');
            }

            $downloadUrl = $bunnyStreamService->getDownloadUrl(
                $file->bunny_video_id
            );

            if (!$downloadUrl) {
                abort(404, 'Video download is not available.');
            }

            return redirect()->away($downloadUrl);
        }

        /*
        |--------------------------------------------------------------------------
        | Bunny Storage Image / Document
        |--------------------------------------------------------------------------
        */
        $downloadUrl = $file->storage_url;

        if (!$downloadUrl) {
            abort(404, 'File not found.');
        }

        $fileName = $file->file_name ?: 'download';

        return response()->streamDownload(
            function () use ($downloadUrl) {

                $stream = fopen($downloadUrl, 'rb');

                if ($stream === false) {
                    abort(404, 'Unable to download file.');
                }

                while (!feof($stream)) {
                    echo fread($stream, 1024 * 1024);
                    flush();
                }

                fclose($stream);
            },
            $fileName
        );
    }

}