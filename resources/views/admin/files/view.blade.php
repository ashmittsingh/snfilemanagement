@extends('layout.admin.app')

@section('content')

@if (session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
@endif

@if (session('error'))
    <div class="alert alert-danger">{{ session('error') }}</div>
@endif

@if(request('success'))
    <div class="alert alert-success">
        {{ request('success') }}
    </div>
@endif

<div class="flex justify-between mb-6 sm:my-8 bg-white p-4">
    <div>
        <h1 class="text-xl sm:text-2xl font-bold text-[#B70F1D] leading-none">
            {{ ucwords($directory->name) }}
        </h1>

        <p class="text-xs sm:text-sm text-gray-400 mt-2">
            <i class="fa-solid fa-calendar-days mr-2"></i>
            Created on {{ $directory->created_at?->format('d F Y, h:i A') }}
        </p>
    </div>

    <a href="{{ route('admin.media.edit', $directory->id) }}">
        <button type="button"
            class="bg-[#B70F1D] inline-flex items-center gap-2 bg-brand hover:bg-brand-dark text-white text-sm font-medium rounded-lg px-5 py-2.5 sm:px-6 sm:py-3 shadow-sm transition-colors cursor-pointer">
            <i class="fa-solid fa-plus"></i>Edit Directory
        </button>
    </a>
</div>

<div class="px-4 sm:px-6 lg:px-8 py-5 sm:py-6 lg:py-8 space-y-5 sm:space-y-6">

    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4 sm:gap-5">

        <div class="bg-white border border-gray-100 rounded-2xl shadow-[0_2px_15px_rgba(0,0,0,0.04)] p-5 sm:p-6">
            <div class="flex items-center gap-4 flex-wrap">
                <div
                    class="w-12 h-12 sm:w-14 sm:h-14 shrink-0 rounded-full bg-indigo-50 flex items-center justify-center">
                    <i class="fa-regular fa-file-lines text-indigo-600 text-xl sm:text-2xl"></i>
                </div>

                <div class="min-w-0 flex-1">
                    <p class="text-xs sm:text-sm text-gray-500">
                        Directory Name
                    </p>

                    <h3 class="mt-1 text-xs sm:text-sm lg:text-base font-semibold text-gray-800 break-words"
                        title="{{ ucwords($directory->name) }}">
                        {{ ucwords($directory->name) }}
                    </h3>

                    <p class="text-[11px] sm:text-xs text-gray-400 mt-0.5">
                        Directory
                    </p>
                </div>
            </div>
        </div>

        <div class="bg-white border border-gray-100 rounded-2xl shadow-[0_2px_15px_rgba(0,0,0,0.04)] p-5 sm:p-6">
            <div class="flex items-center gap-4 flex-wrap">
                <div
                    class="w-12 h-12 sm:w-14 sm:h-14 shrink-0 rounded-full bg-[#B70F1D]/10 flex items-center justify-center">
                    <i class="fa-regular fa-folder text-[#B70F1D] text-xl sm:text-2xl"></i>
                </div>

                <div class="min-w-0 flex-1">
                    <p class="text-xs sm:text-sm text-gray-500">
                        Total {{ ucfirst($directory->type) }}
                    </p>

                    <h3 class="mt-1 text-lg sm:text-xl font-semibold text-gray-800">
                        {{ $directory->files->count() }}
                    </h3>

                    <p class="text-[11px] sm:text-xs text-gray-400 mt-0.5">
                        Files uploaded
                    </p>
                </div>
            </div>
        </div>

        <div class="bg-white border border-gray-100 rounded-2xl shadow-[0_2px_15px_rgba(0,0,0,0.04)] p-5 sm:p-6">
            <div class="flex items-center gap-4 flex-wrap">
                <div
                    class="w-12 h-12 sm:w-14 sm:h-14 shrink-0 rounded-full bg-green-50 flex items-center justify-center">
                    <i class="fa-regular fa-calendar-days text-green-600 text-xl sm:text-2xl"></i>
                </div>

                <div class="min-w-0 flex-1">
                    <p class="text-xs sm:text-sm text-gray-500">
                        Last updated
                    </p>

                    <h3 class="mt-1 text-xs sm:text-sm lg:text-base font-semibold text-gray-800 break-words">
                        {{ $directory->updated_at?->format('d F Y, h:i A') }}
                    </h3>
                </div>
            </div>
        </div>

    </div>

    <div class="bg-white border border-gray-100 rounded-2xl shadow-[0_2px_20px_rgba(0,0,0,0.05)] overflow-hidden">

        <div class="px-4 sm:px-6 lg:px-8 pt-5 sm:pt-6 lg:pt-7 pb-4">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div>
                    <h2 class="text-lg sm:text-xl lg:text-2xl font-semibold text-[#B70F1D]">
                        Uploaded {{ ucfirst($directory->type) }}s
                    </h2>

                    <p class="mt-1 text-xs sm:text-sm text-gray-400">
                        All {{ $directory->type }}s uploaded in this directory
                    </p>
                </div>
            </div>
        </div>

        <div class="hidden min-[1624px]:block px-4 sm:px-6 lg:px-8 pb-6">
            <div class="overflow-hidden border border-gray-100 rounded-xl">

                <table class="w-full table-fixed border-collapse text-left">

                    <thead>
                        <tr class="bg-[#B70F1D]/10">
                            <th
                                class="px-4 lg:px-6 py-4 text-xs lg:text-sm font-semibold text-[#B70F1D] whitespace-nowrap">
                                File Name
                            </th>

                            <th
                                class="px-4 lg:px-6 py-4 text-xs lg:text-sm font-semibold text-[#B70F1D] whitespace-nowrap">
                                File Type
                            </th>

                            <!-- <th
                                class="px-4 lg:px-6 py-4 text-xs lg:text-sm font-semibold text-[#B70F1D] whitespace-nowrap">
                                File Size
                            </th> -->

                            <th
                                class="px-4 lg:px-6 py-4 text-xs lg:text-sm font-semibold text-[#B70F1D] whitespace-nowrap">
                                Date Uploaded
                            </th>

                            <th
                                class="px-4 lg:px-6 py-4 text-xs lg:text-sm font-semibold text-[#B70F1D] whitespace-nowrap">
                                Action
                            </th>
                        </tr>
                    </thead>

                    <tbody class="divide-y divide-gray-100">

                        @forelse ($directory->files as $file)

                            @php
                                $extension = strtolower(pathinfo($file->file_name, PATHINFO_EXTENSION));

                                $fileType = match ($extension) {
                                    'pdf' => 'PDF',
                                    'doc' => 'DOC',
                                    'docx' => 'DOCX',
                                    'mp4' => 'MP4',
                                    'jpg', 'jpeg' => 'JPG',
                                    'png' => 'PNG',
                                    'webp' => 'WEBP',
                                    default => strtoupper($extension ?: 'FILE'),
                                };

                                $fileIcon = match ($extension) {
                                    'pdf' => 'fa-file-pdf',
                                    'doc', 'docx' => 'fa-file-word',
                                    'mp4' => 'fa-file-video',
                                    'jpg', 'jpeg', 'png', 'webp' => 'fa-file-image',
                                    default => 'fa-file',
                                };
                            @endphp

                            <tr class="hover:bg-gray-50/70 transition-colors">

                                <td class="px-4 lg:px-6 py-4 lg:py-5">
                                    <div class="flex items-center gap-3 min-w-[230px]">
                                        <div
                                            class="w-9 h-9 shrink-0 rounded-lg bg-[#B70F1D]/10 flex items-center justify-center">
                                            <i class="fa-regular {{ $fileIcon }} text-[#B70F1D]"></i>
                                        </div>

                                        <span class="text-xs sm:text-sm text-gray-700 font-medium truncate"
                                            title="{{ ucwords($file->file_name) }}">
                                            {{ ucwords($file->file_name) }}
                                        </span>
                                    </div>
                                </td>

                                <td class="px-4 lg:px-6 py-4 lg:py-5">
                                    <span
                                        class="inline-flex items-center justify-center px-3 py-1.5 rounded-md border border-[#B70F1D]/30 bg-[#B70F1D]/5 text-[#B70F1D] text-xs font-semibold">
                                        {{ $fileType }}
                                    </span>
                                </td>

                                <td class="px-4 lg:px-6 py-4 lg:py-5">
                                    <span class="text-xs sm:text-sm text-gray-600 whitespace-nowrap">
                                        —
                                    </span>
                                </td>

                                <td class="px-4 lg:px-6 py-4 lg:py-5">
                                    <span class="text-xs sm:text-sm text-gray-600 whitespace-nowrap">
                                        {{ $file->created_at?->format('d F Y, h:i A') }}
                                    </span>
                                </td>

                                <td class="px-4 lg:px-6 py-4 lg:py-5">
                                    <div class="flex items-center gap-2">
                                        <a href="{{ $file->storage_url ?? $file->bunny_play_url ?? '#' }}"
                                            @if($file->storage_url || $file->bunny_play_url) target="_blank" @endif
                                            title="Download"
                                            aria-label="Download file"
                                            class="w-10 h-10 inline-flex items-center justify-center rounded-lg border border-[#B70F1D]/40 text-[#B70F1D] hover:bg-[#B70F1D] hover:text-white transition-all duration-200">
                                            <i class="fa-solid fa-download text-xs"></i>
                                        </a>
                                    </div>
                                </td>

                            </tr>

                        @empty

                            <tr>
                                <td colspan="5" class="px-4 lg:px-6 py-8 text-center text-sm text-gray-500">
                                    No files uploaded in this directory.
                                </td>
                            </tr>

                        @endforelse

                    </tbody>

                </table>

            </div>
        </div>

        <div
            class="min-[1624px]:hidden px-4 pb-5 grid grid-cols-1 min-[640px]:grid-cols-2 min-[1024px]:grid-cols-3 gap-4">

            @forelse ($directory->files as $file)

                @php
                    $extension = strtolower(pathinfo($file->file_name, PATHINFO_EXTENSION));

                    $fileType = match ($extension) {
                        'pdf' => 'PDF',
                        'doc' => 'DOC',
                        'docx' => 'DOCX',
                        'mp4' => 'MP4',
                        'jpg', 'jpeg' => 'JPG',
                        'png' => 'PNG',
                        'webp' => 'WEBP',
                        default => strtoupper($extension ?: 'FILE'),
                    };

                    $fileIcon = match ($extension) {
                        'pdf' => 'fa-file-pdf',
                        'doc', 'docx' => 'fa-file-word',
                        'mp4' => 'fa-file-video',
                        'jpg', 'jpeg', 'png', 'webp' => 'fa-file-image',
                        default => 'fa-file',
                    };
                @endphp

                <div class="border border-gray-200 rounded-xl p-4 bg-white shadow-[0_2px_10px_rgba(0,0,0,0.03)]">

                    <div class="flex items-start gap-3">

                        <div class="w-10 h-10 shrink-0 rounded-lg bg-[#B70F1D]/10 flex items-center justify-center">
                            <i class="fa-regular {{ $fileIcon }} text-[#B70F1D]"></i>
                        </div>

                        <div class="min-w-0 flex-1">

                            <p class="text-sm font-semibold text-gray-800 break-all leading-5">
                                {{ $file->file_name }}
                            </p>

                            <span
                                class="inline-flex mt-2 px-2.5 py-1 rounded-md border border-[#B70F1D]/30 bg-[#B70F1D]/5 text-[#B70F1D] text-[10px] font-semibold">
                                {{ $fileType }}
                            </span>

                        </div>

                    </div>

                    <div class="mt-4 pt-3 border-t border-gray-100 grid grid-cols-2 gap-4">

                        <!-- <div>
                            <p class="text-[10px] uppercase tracking-wider font-semibold text-gray-400">
                                File Size
                            </p>

                            <p class="mt-1 text-xs sm:text-sm text-gray-600">
                                —
                            </p>
                        </div> -->

                        <div>
                            <p class="text-[10px] uppercase tracking-wider font-semibold text-gray-400">
                                Date Uploaded
                            </p>

                            <p class="mt-1 text-xs sm:text-sm text-gray-600">
                                {{ $file->created_at?->format('d F Y') }}
                            </p>
                        </div>

                    </div>

                    <div class="mt-4 pt-3 border-t border-gray-100 flex gap-2">

                        <a href="{{ $file->storage_url ?? $file->bunny_play_url ?? '#' }}"
                            @if($file->storage_url || $file->bunny_play_url) target="_blank" @endif
                            class="flex-1 h-9 inline-flex items-center justify-center gap-2 rounded-lg border border-[#B70F1D]/40 text-[#B70F1D] text-xs font-medium hover:bg-[#B70F1D] hover:text-white transition-all duration-200">
                            <i class="fa-solid fa-download"></i>
                            Download
                        </a>

                    </div>

                </div>

            @empty

                <div class="col-span-full text-center text-sm text-gray-500 py-8">
                    No files uploaded in this directory.
                </div>

            @endforelse

        </div>

    </div>

</div>

@endsection
