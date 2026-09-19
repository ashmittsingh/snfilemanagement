@forelse ($directories as $directory)

                @php
                    $typeLabel = match ($directory->type) {
                        'image' => 'Images',
                        'reel' => 'Reels',
                        'document' => 'Documents',
                        default => ucfirst($directory->type),
                    };

                    $typeIcon = match ($directory->type) {
                        'image' => 'fa-image',
                        'reel' => 'fa-video',
                        'document' => 'fa-file',
                        default => 'fa-folder',
                    };
                @endphp

                <div
                    class="group bg-white border border-gray-100 rounded-2xl p-4 min-[390px]:p-4.5 shadow-[0_2px_12px_rgba(0,0,0,0.3)] hover:shadow-[0_6px_24px_rgba(183,15,29,0.14)] hover:border-[#B70F1D]/30 transition-all duration-200">

                    <div class="flex items-start gap-3">

                        <div
                            class="w-11 h-11 min-[390px]:w-12 min-[390px]:h-12 shrink-0 rounded-xl bg-[#B70F1D]/8 flex items-center justify-center text-[#B70F1D] group-hover:bg-[#B70F1D]/12 transition-colors">

                            <i
                                class="fa-solid {{ $typeIcon }} text-base min-[390px]:text-lg">
                            </i>

                        </div>

                        <div class="min-w-0 flex-1 pt-0.5">

                            <h3
                                class="text-sm min-[390px]:text-base font-semibold text-gray-800 leading-snug break-words line-clamp-2"
                                title="{{ ucwords($directory->name) }}">

                                {{ ucwords($directory->name) }}

                            </h3>

                            <span
                                class="inline-flex items-center gap-1 mt-1.5 text-[10px] font-semibold uppercase tracking-[0.08em] text-[#B70F1D] bg-[#B70F1D]/8 rounded-full px-2 py-0.5">

                                <i class="fa-solid fa-circle text-[5px]"></i>

                                {{ $typeLabel }}

                            </span>

                        </div>

                    </div>


                    <div class="mt-4 pt-3.5 border-t border-gray-100 grid grid-cols-2 gap-3">

                        <div>

                            <p
                                class="text-[10px] font-semibold uppercase tracking-[0.1em] text-gray-400 mb-1 flex items-center gap-1">

                                <i class="fa-solid fa-layer-group text-[9px]"></i>

                                Count

                            </p>

                            <p class="text-xs min-[390px]:text-sm font-medium text-gray-700">

                                {{ $directory->files_count }}
                                {{ $directory->files_count === 1 ? 'File' : 'Files' }}

                            </p>

                        </div>


                        <div>

                            <p
                                class="text-[10px] font-semibold uppercase tracking-[0.1em] text-gray-400 mb-1 flex items-center gap-1">

                                <i class="fa-regular fa-calendar text-[9px]"></i>

                                Date Added

                            </p>

                            <p class="text-xs min-[390px]:text-sm font-medium text-gray-700">

                                {{ $directory->created_at->format('d M Y') }}

                            </p>

                        </div>

                    </div>

@if (
    $directory->type === 'reel' &&
    $directory->ready_files_count < $directory->files_count
)
    <div 
        id="reel-progress-{{ $directory->id }}" 
        data-progress-url="{{ route('admin.reel.progress', $directory->id) }}" 
        class="mt-4 pt-3.5 border-t border-gray-100">

        <div class="flex items-center justify-between gap-2">
            <p 
                class="text-[10px] font-semibold uppercase tracking-[0.1em] text-gray-400 flex items-center gap-1">
                <i class="fa-solid fa-spinner text-[9px]"></i>
                Status
            </p>

            <span 
                id="reel-progress-label-{{ $directory->id }}" 
                class="text-[10px] font-semibold text-[#B70F1D]">
                Processing 0%
            </span>
        </div>

        <div class="mt-2 w-full h-2 bg-gray-100 rounded-full overflow-hidden">
            <div 
                id="reel-progress-bar-{{ $directory->id }}" 
                class="h-full bg-[#B70F1D] rounded-full transition-all duration-300" 
                style="width: 0%;">
            </div>
        </div>
    </div>
@endif


                    <div class="flex items-center gap-2 mt-4 pt-3.5 border-t border-gray-100">

                        {{-- Edit --}}

                            <a
                                href="{{ route('admin.media.edit', $directory->id) }}"
                                class="flex-1">

                                <button
                                    type="button"
                                    class="w-full h-9 flex items-center justify-center gap-1.5 rounded-lg border border-[#B70F1D] text-[#B70F1D] text-xs min-[390px]:text-sm font-medium hover:bg-[#B70F1D] hover:text-white active:scale-[0.97] transition-all duration-150"
                                    aria-label="Edit directory">

                                    <i class="fa-solid fa-pen text-xs"></i>

                                    <span class="hidden min-[360px]:inline">
                                        Edit
                                    </span>

                                </button>

                            </a>



                        {{-- View --}}
                        <a
                            href="{{ route('admin.media.view', $directory->id) }} "
                            class="flex-1">

                            <button
                                type="button"
                                class="w-full h-9 flex items-center justify-center gap-1.5 rounded-lg border border-[#B70F1D] text-[#B70F1D] text-xs min-[390px]:text-sm font-medium hover:bg-[#B70F1D] hover:text-white active:scale-[0.97] transition-all duration-150"
                                aria-label="View file">

                                <i class="fa-solid fa-eye text-xs"></i>

                                <span class="hidden min-[360px]:inline">
                                    View
                                </span>

                            </button>

                        </a>


                        {{-- Delete --}}
                        <div class="flex-1">

                            <button
                                type="button"
                                onclick="deleteItem(this, {{ $directory->id }})"
                                class="w-full h-9 flex items-center justify-center gap-1.5 rounded-lg border border-[#B70F1D] text-[#B70F1D] text-xs min-[390px]:text-sm font-medium hover:bg-[#B70F1D] hover:text-white active:scale-[0.97] transition-all duration-150"
                                aria-label="Delete directory">

                                <i class="fa-solid fa-trash-can text-xs"></i>

                                <span class="hidden min-[360px]:inline">
                                    Delete
                                </span>

                            </button>

                        </div>

                    </div>

                </div>

            @empty

                <div class="col-span-full py-16 text-center">

                    <div
                        class="w-16 h-16 mx-auto rounded-full bg-[#B70F1D]/10 flex items-center justify-center">

                        <i class="fa-regular fa-folder-open text-2xl text-[#B70F1D]"></i>

                    </div>

                    <h3 class="mt-4 text-base font-semibold text-gray-800">
                        No directories found
                    </h3>

                    <p class="mt-1 text-sm text-gray-400">

                        @if ($search || $type)
                            Try changing your search or filter.
                        @else
                            No files have been added yet.
                        @endif

                    </p>

                </div>

            @endforelse