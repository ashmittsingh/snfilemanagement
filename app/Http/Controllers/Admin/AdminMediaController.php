<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Jobs\UploadMediaToBunny;
use App\Jobs\UploadReelToBunny;
use App\Models\MediaDirectory;
use App\Models\MediaFile;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\View\View;
use Throwable;
use App\Jobs\DeleteMediaFileFromBunny;
use Illuminate\Support\Facades\Log;

class AdminMediaController extends Controller
{
    /**
     * Show Add Media page.
     */
    public function add(Request $request): View
    {
        $type = $this->normalizeType(
            $request->input('type')
        );

        return view('admin.media.add', [
            'pageTitle' => 'Add Media',
            'selectedType' => $type,
        ]);
    }

    /**
     * Check whether a directory name already exists.
     */
    public function checkName(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
            ],
            'id' => [
                'nullable',
                'integer',
                'exists:media_directories,id',
            ],
        ]);

        $name = trim($validated['name']);

        $exists = MediaDirectory::query()
            ->where('name', $name)
            ->when(
                !empty($validated['id']),
                fn($query) => $query->where(
                    'id',
                    '!=',
                    (int) $validated['id']
                )
            )
            ->exists();

        return response()->json([
            'status' => true,
            'exists' => $exists,
        ]);
    }

    /**
     * Normalize and validate supported media type.
     */
    private function normalizeType(?string $type): ?string
    {
        $type = strtolower(trim((string) $type));

        return in_array(
            $type,
            ['image', 'reel', 'document'],
            true
        )
            ? $type
            : null;
    }


    /**
     * Store a media directory and queue each file independently.
     */
    public function store(Request $request): JsonResponse
    {
        $type = $this->normalizeType(
            $request->input('type')
        );

        if (!$type) {
            return response()->json([
                'status' => false,
                'message' => 'Please correct the highlighted errors.',
                'errors' => [
                    'type' => [
                        'Please select a valid file type.'
                    ],
                ],
            ], 422);
        }

        $validationRules = [
            'directory_name' => [
                'required',
                'string',
                'max:255',
                'regex:/^[a-zA-Z0-9 _-]+$/',
                'unique:media_directories,name',
            ],

            'description' => [
                'required',
                'string',
                'max:1000',
            ],

            'type' => [
                'required',
                'in:image,reel,document',
            ],

            'files' => [
                'required',
                'array',
                'min:1',
                'max:' . $this->maxFiles($type),
            ],

            'files.*' => [
                'required',
                'file',
                ...$this->fileValidationRules($type),
            ],
        ];

        $validator = Validator::make(
            $request->all(),
            $validationRules
        );

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Please correct the highlighted errors.',
                'errors' => $validator->errors(),
            ], 422);
        }

        $storedFiles = [];

        try {
            /*
            |--------------------------------------------------------------------------
            | 1. Store uploaded files temporarily
            |--------------------------------------------------------------------------
            */
            foreach ($request->file('files', []) as $file) {

                $storedFiles[] = [
                    'path' => $file->store(
                        'temp/media/' . $type,
                        'local'
                    ),
                    'name' => $file->getClientOriginalName(),
                ];
            }

            /*
            |--------------------------------------------------------------------------
            | 2. Create directory + media records atomically
            |--------------------------------------------------------------------------
            */
            DB::transaction(function () use ($request, $type, $storedFiles) {

                $directory = MediaDirectory::create([
                    'name' => trim($request->input('directory_name')),
                    'description' => trim($request->input('description')),
                    'type' => $type,
                ]);

                foreach ($storedFiles as $storedFile) {

                    $mediaFile = MediaFile::create([
                        'media_directory_id' => $directory->id,
                        'file_name' => $storedFile['name'],
                        'file_path' => $storedFile['path'],
                        'storage_path' => null,
                        'storage_url' => null,
                        'bunny_video_id' => null,
                        'bunny_play_url' => null,
                        'bunny_embed_url' => null,
                        'bunny_status' => 'pending',
                    ]);

                    if ($type === 'reel') {

                        UploadReelToBunny::dispatch($mediaFile->id)
                            ->afterCommit();

                    } else {

                        UploadMediaToBunny::dispatch($mediaFile->id)
                            ->afterCommit();
                    }
                }
            });
            /*
            |--------------------------------------------------------------------------
            | 3. Queue one independent job per file
            |--------------------------------------------------------------------------
            */


            return response()->json([
                'status' => true,
                'message' => 'Media uploaded successfully and processing has started.',
                'redirect' => route('admin.dashboard'),
            ]);

        } catch (Throwable $exception) {

            /*
            |--------------------------------------------------------------------------
            | Cleanup temporary files if DB operation fails
            |--------------------------------------------------------------------------
            */
            foreach ($storedFiles as $storedFile) {

                if (
                    !empty($storedFile['path']) &&
                    Storage::disk('local')->exists(
                        $storedFile['path']
                    )
                ) {
                    Storage::disk('local')->delete(
                        $storedFile['path']
                    );
                }
            }

            report($exception);

            return response()->json([
                'status' => false,
                'message' => 'Unable to upload media. Please try again.',
            ], 500);
        }
    }

    /**
     * Maximum number of files allowed for each media type.
     */
    private function maxFiles(string $type): int
    {
        return match ($type) {
            'reel' => 5,
            'image' => 10,
            'document' => 10,
            default => 0,
        };
    }

    /**
     * File validation rules based on media type.
     */
    private function fileValidationRules(string $type): array
    {
        return match ($type) {

            'image' => [
                'mimes:jpg,jpeg,png,webp',
                'max:10240',
            ],

            'reel' => [
                'mimes:mp4',
                'max:51200',
            ],

            'document' => [
                'mimes:pdf,doc,docx',
                'max:51200',
            ],

            default => [
                'prohibited',
            ],
        };
    }

    /**
     * Show Edit Media Directory page.
     */
    public function edit(MediaDirectory $directory): View
    {
        $directory->load([
            'files:id,media_directory_id,file_name,file_path,storage_path,storage_url,bunny_video_id,bunny_play_url,bunny_embed_url,bunny_status',
        ]);

        return view('admin.media.edit', [
            'pageTitle' => 'Edit Media',
            'directory' => $directory,
        ]);
    }


    /**
     * Update an existing media directory.
     */
    /**
     * Update an existing media directory.
     */
    public function update(
        Request $request,
        MediaDirectory $directory
    ): JsonResponse {
        $type = $this->normalizeType(
            $directory->type
        );

        if (!$type) {
            return response()->json([
                'status' => false,
                'message' => 'Invalid media directory type.',
            ], 422);
        }

        $validator = Validator::make(
            $request->all(),
            [
                'directory_name' => [
                    'required',
                    'string',
                    'max:255',
                    'regex:/^[a-zA-Z0-9 _-]+$/',
                    'unique:media_directories,name,' . $directory->id,
                ],

                'description' => [
                    'required',
                    'string',
                    'max:1000',
                ],

                'delete_files' => [
                    'nullable',
                    'array',
                ],

                'delete_files.*' => [
                    'integer',
                    'distinct',
                ],

                'files' => [
                    'nullable',
                    'array',
                ],

                'files.*' => [
                    'required',
                    'file',
                    ...$this->fileValidationRules($type),
                ],
            ]
        );

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Please correct the highlighted errors.',
                'errors' => $validator->errors(),
            ], 422);
        }

        $deleteFileIds = collect(
            $request->input('delete_files', [])
        )
            ->map(fn($id) => (int) $id)
            ->unique()
            ->values();

        $filesToDelete = $directory->files()
            ->whereIn('id', $deleteFileIds)
            ->get();

        // Log::info('Media delete debug', [
        //     'directory_id' => $directory->id,
        //     'delete_file_ids' => $deleteFileIds->all(),
        //     'directory_file_ids' => $directory->files()
        //         ->pluck('id')
        //         ->all(),
        //     'matched_file_ids' => $filesToDelete
        //         ->pluck('id')
        //         ->all(),
        // ]);

        /*
        |--------------------------------------------------------------------------
        | Security: IDs must belong to this directory
        |--------------------------------------------------------------------------
        */
        if (
            $deleteFileIds->count() !==
            $filesToDelete->count()
        ) {
            return response()->json([
                'status' => false,
                'message' => 'Invalid file selection.',
                'errors' => [
                    'files' => [
                        'One or more selected files do not belong to this directory.',
                    ],
                ],
            ], 422);
        }

        $newFiles =
            $request->file('files', []);

        $remainingFilesCount =
            $directory->files()->count() -
            $filesToDelete->count();

        $finalFilesCount =
            $remainingFilesCount +
            count($newFiles);

        if (
            $finalFilesCount >
            $this->maxFiles($type)
        ) {
            return response()->json([
                'status' => false,
                'message' => 'Please correct the highlighted errors.',
                'errors' => [
                    'files' => [
                        "You can have maximum {$this->maxFiles($type)} files in this directory.",
                    ],
                ],
            ], 422);
        }

        $storedFiles = [];

        try {

            /*
            |--------------------------------------------------------------------------
            | Store new files temporarily
            |--------------------------------------------------------------------------
            */
            foreach ($newFiles as $file) {

                $storedFiles[] = [
                    'path' => $file->store(
                        'temp/media/' . $type,
                        'local'
                    ),
                    'name' =>
                        $file->getClientOriginalName(),
                ];
            }

            /*
            |--------------------------------------------------------------------------
            | Capture Bunny deletion data before DB delete
            |--------------------------------------------------------------------------
            */
            $deleteJobs =
                $filesToDelete
                    ->map(function (MediaFile $mediaFile) use ($type) {

                        return [
                            'media_file_id' =>
                                $mediaFile->id,

                            'type' =>
                                $type,

                            'storage_path' =>
                                $mediaFile->storage_path,

                            'bunny_video_id' =>
                                $mediaFile->bunny_video_id,
                        ];
                    })
                    ->values()
                    ->all();

            DB::transaction(
                function () use ($request, $directory, $type, $filesToDelete, $storedFiles, $deleteJobs) {

                    /*
                    |--------------------------------------------------------------------------
                    | Delete selected DB records
                    |--------------------------------------------------------------------------
                    */
                    foreach (
                        $filesToDelete as $mediaFile
                    ) {

                        if (
                            filled(
                                $mediaFile->file_path
                            )
                        ) {

                            $disk =
                                Storage::disk('local');

                            if (
                                $disk->exists(
                                    $mediaFile->file_path
                                )
                            ) {
                                $disk->delete(
                                    $mediaFile->file_path
                                );
                            }
                        }

                        $mediaFile->delete();
                    }

                    /*
                    |--------------------------------------------------------------------------
                    | Update directory
                    |--------------------------------------------------------------------------
                    */
                    $directory->update([
                        'name' => trim(
                            $request->input(
                                'directory_name'
                            )
                        ),

                        'description' => trim(
                            $request->input(
                                'description'
                            )
                        ),
                    ]);

                    /*
                    |--------------------------------------------------------------------------
                    | Create new MediaFile records
                    |--------------------------------------------------------------------------
                    */
                    foreach (
                        $storedFiles as $storedFile
                    ) {

                        $mediaFile =
                            MediaFile::create([
                                'media_directory_id' =>
                                    $directory->id,

                                'file_name' =>
                                    $storedFile['name'],

                                'file_path' =>
                                    $storedFile['path'],

                                'storage_path' =>
                                    null,

                                'storage_url' =>
                                    null,

                                'bunny_video_id' =>
                                    null,

                                'bunny_play_url' =>
                                    null,

                                'bunny_embed_url' =>
                                    null,

                                'bunny_status' =>
                                    'pending',
                            ]);

                        if ($type === 'reel') {

                            UploadReelToBunny::dispatch(
                                $mediaFile->id
                            )->afterCommit();

                        } else {

                            UploadMediaToBunny::dispatch(
                                $mediaFile->id
                            )->afterCommit();
                        }
                    }

                    /*
                    |--------------------------------------------------------------------------
                    | Bunny deletion jobs
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
                }
            );

            return response()->json([
                'status' => true,
                'message' => 'Media updated successfully.',
                'redirect' =>
                    route('admin.dashboard'),
            ]);

        } catch (Throwable $exception) {

            /*
            |--------------------------------------------------------------------------
            | Cleanup newly stored temporary files
            |--------------------------------------------------------------------------
            */
            foreach (
                $storedFiles as $storedFile
            ) {

                if (
                    !empty($storedFile['path']) &&
                    Storage::disk('local')->exists(
                        $storedFile['path']
                    )
                ) {

                    Storage::disk('local')->delete(
                        $storedFile['path']
                    );
                }
            }

            report($exception);

            return response()->json([
                'status' => false,
                'message' =>
                    'Unable to update media. Please try again.',
            ], 500);
        }
    }
}