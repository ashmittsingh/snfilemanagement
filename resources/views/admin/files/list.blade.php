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

    <div
        class="w-full mb-6 bg-white p-3 min-[360px]:p-4 min-[480px]:p-5 sm:p-5 md:p-6 lg:p-6 xl:p-7 min-[1440px]:p-8 min-[1920px]:p-9">

        <div class="flex flex-col gap-4 min-[480px]:gap-5 xl:flex-row xl:items-center xl:justify-between xl:gap-6">

            <div class="min-w-0 shrink-0">
                <h1
                    class="text-base min-[360px]:text-lg min-[390px]:text-xl sm:text-2xl lg:text-3xl font-bold text-[#B70F1D] leading-tight">
                    Files
                </h1>

                <p class="mt-1 text-[11px] min-[360px]:text-xs min-[480px]:text-sm text-gray-400 leading-relaxed">
                    Manage and organize your files
                </p>
            </div>

            <form
                method="GET"
                action="{{ url()->current() }}"
                class="w-full xl:w-auto xl:min-w-0 flex flex-col sm:flex-row gap-2.5 min-[480px]:gap-3 lg:gap-3.5">

                <div
                    class="relative w-full sm:w-[240px] md:w-[260px] lg:w-[300px] xl:w-[280px] min-[1440px]:w-[340px] min-[1920px]:w-[400px] shrink-0">

                    <i
                        class="fa-solid fa-magnifying-glass absolute left-3 min-[390px]:left-3.5 top-1/2 -translate-y-1/2 text-[#B70F1D]/70 text-xs min-[390px]:text-sm pointer-events-none">
                    </i>

                    <input
                        type="text"
                        name="search"
                        id="searchInput"
                        value="{{ $search }}"
                        placeholder="Search by Image, Document, Reels"
                        class="block w-full h-10 min-[390px]:h-11 pl-9 min-[390px]:pl-10 pr-3 min-[390px]:pr-4 text-[11px] min-[390px]:text-xs sm:text-sm rounded-lg border border-[#B70F1D]/60 text-gray-600 placeholder-gray-400 bg-white focus:outline-none focus:ring-1 focus:ring-[#B70F1D] focus:border-[#B70F1D] transition-all duration-200"
                    />

                </div>

                <input
                    type="hidden"
                    name="type"
                    id="typeFilterInput"
                    value="{{ $type ?? '' }}"
                >

                <div
                    class="grid grid-cols-2 min-[480px]:grid-cols-2 sm:flex sm:flex-wrap gap-2.5 min-[360px]:gap-3 sm:gap-2.5 lg:gap-3">

                    <div class="relative w-full sm:w-auto min-w-0" id="filterWrapper">

                        <button
                            type="button"
                            id="filterButton"
                            aria-haspopup="true"
                            aria-expanded="false"
                            class="w-full sm:w-auto h-10 min-[390px]:h-11 inline-flex items-center justify-center gap-1.5 min-[390px]:gap-2 border border-[#B70F1D]/60 text-[#B70F1D] text-[11px] min-[390px]:text-xs sm:text-sm font-medium rounded-lg px-2.5 min-[390px]:px-3.5 sm:px-4 hover:bg-[#B70F1D]/5 active:scale-[0.98] transition-all duration-200 cursor-pointer whitespace-nowrap">

                            <i class="fa-solid fa-filter text-[10px] min-[390px]:text-xs"></i>

                            <span>
                                @switch($type)
                                    @case('image')
                                        Images
                                        @break

                                    @case('reel')
                                        Reels
                                        @break

                                    @case('document')
                                        Documents
                                        @break

                                    @default
                                        Type
                                @endswitch
                            </span>

                            <i
                                id="filterArrow"
                                class="fa-solid fa-chevron-down text-[8px] min-[390px]:text-[9px] transition-transform duration-200">
                            </i>

                        </button>


                        <div
                            id="filterDropdown"
                            class="hidden absolute left-0 right-0 min-[360px]:left-auto min-[360px]:right-0 top-[calc(100%+8px)] w-full min-[360px]:w-44 bg-white rounded-xl border border-gray-100 shadow-[0_12px_35px_rgba(0,0,0,0.12)] overflow-hidden z-[100]">

                            <button
                                type="button"
                                data-filter=""
                                class="filter-option w-full flex items-center gap-2.5 px-3.5 py-2.5 text-xs min-[390px]:text-sm text-gray-600 hover:bg-[#B70F1D]/5 hover:text-[#B70F1D] active:bg-[#B70F1D]/10 transition-colors">

                                <i class="fa-solid fa-layer-group text-xs w-4 shrink-0"></i>

                                <span>
                                    All
                                </span>

                            </button>


                            <button
                                type="button"
                                data-filter="image"
                                class="filter-option w-full flex items-center gap-2.5 px-3.5 py-2.5 text-xs min-[390px]:text-sm text-gray-600 hover:bg-[#B70F1D]/5 hover:text-[#B70F1D] active:bg-[#B70F1D]/10 transition-colors border-t border-gray-100">

                                <i class="fa-solid fa-image text-xs w-4 shrink-0"></i>

                                <span>
                                    Images
                                </span>

                            </button>


                            <button
                                type="button"
                                data-filter="reel"
                                class="filter-option w-full flex items-center gap-2.5 px-3.5 py-2.5 text-xs min-[390px]:text-sm text-gray-600 hover:bg-[#B70F1D]/5 hover:text-[#B70F1D] active:bg-[#B70F1D]/10 transition-colors border-t border-gray-100">

                                <i class="fa-solid fa-video text-xs w-4 shrink-0"></i>

                                <span>
                                    Reels
                                </span>

                            </button>


                            <button
                                type="button"
                                data-filter="document"
                                class="filter-option w-full flex items-center gap-2.5 px-3.5 py-2.5 text-xs min-[390px]:text-sm text-gray-600 hover:bg-[#B70F1D]/5 hover:text-[#B70F1D] active:bg-[#B70F1D]/10 transition-colors border-t border-gray-100">

                                <i class="fa-solid fa-file text-xs w-4 shrink-0"></i>

                                <span>
                                    Documents
                                </span>

                            </button>

                        </div>

                    </div>


                    <a href="{{ route('admin.media.add') }}" class="w-full sm:w-auto">
                        <button
                            type="button"
                            class="w-full sm:w-auto h-10 min-[390px]:h-11 inline-flex items-center justify-center gap-1.5 min-[390px]:gap-2 bg-[#B70F1D] hover:bg-[#9c0c18] active:bg-[#8f0b16] text-white text-[11px] min-[390px]:text-xs sm:text-sm font-medium rounded-lg px-2.5 min-[390px]:px-4 sm:px-5 shadow-sm hover:shadow-md active:scale-[0.98] transition-all duration-200 cursor-pointer whitespace-nowrap">

                            <i class="fa-solid fa-plus text-[10px] min-[390px]:text-xs"></i>

                            <span>
                                Add Media
                            </span>

                        </button>
                    </a>


                    

                    

                </div>

            </form>

        </div>

    </div>


    {{-- =========================================================
        DYNAMIC STATISTICS
    ========================================================== --}}
    <div
        class="grid grid-cols-1 min-[640px]:grid-cols-2 md:grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-5 mx-3 min-[390px]:mx-4 sm:mx-6 lg:mx-8 min-[1024px]:mx-8 min-[1280px]:mx-10 min-[1440px]:mx-12 min-[1920px]:mx-14">

        {{-- Total Directory --}}
        <div
            class="bg-white border border-gray-100 rounded-2xl shadow-[0_2px_15px_rgba(0,0,0,0.04)] p-5 sm:p-6">

            <div class="flex items-center gap-4 flex-wrap">

                <div
                    class="w-12 h-12 sm:w-14 sm:h-14 shrink-0 rounded-full bg-[#B70F1D]/10 flex items-center justify-center">

                    <i class="fa-regular fa-folder text-[#B70F1D] text-xl sm:text-2xl"></i>

                </div>

                <div class="min-w-0 flex-1">

                    <p class="text-xs sm:text-sm text-gray-500">
                        Total Directory
                    </p>

                    <h3 class="mt-1 text-lg sm:text-xl font-semibold text-gray-800">
                        {{ $totalDirectories }}
                    </h3>

                </div>

            </div>

        </div>


        {{-- Total Images --}}
        <div
            class="bg-white border border-gray-100 rounded-2xl shadow-[0_2px_15px_rgba(0,0,0,0.04)] p-5 sm:p-6">

            <div class="flex items-center gap-4 flex-wrap">

                <div
                    class="w-12 h-12 sm:w-14 sm:h-14 shrink-0 rounded-full bg-[#B70F1D]/10 flex items-center justify-center">

                    <i class="fa-solid fa-image text-[#B70F1D] text-xl sm:text-2xl"></i>

                </div>

                <div class="min-w-0 flex-1">

                    <p class="text-xs sm:text-sm text-gray-500">
                        Total Images
                    </p>

                    <h3 class="mt-1 text-lg sm:text-xl font-semibold text-gray-800">
                        {{ $totalImages }}
                    </h3>

                </div>

            </div>

        </div>


        {{-- Total Reels --}}
        <div
            class="bg-white border border-gray-100 rounded-2xl shadow-[0_2px_15px_rgba(0,0,0,0.04)] p-5 sm:p-6">

            <div class="flex items-center gap-4 flex-wrap">

                <div
                    class="w-12 h-12 sm:w-14 sm:h-14 shrink-0 rounded-full bg-[#B70F1D]/10 flex items-center justify-center">

                    <i class="fa-solid fa-video text-[#B70F1D] text-xl sm:text-2xl"></i>

                </div>

                <div class="min-w-0 flex-1">

                    <p class="text-xs sm:text-sm text-gray-500">
                        Total Reels
                    </p>

                    <h3 class="mt-1 text-lg sm:text-xl font-semibold text-gray-800">
                        {{ $totalReels }}
                    </h3>

                </div>

            </div>

        </div>


        {{-- Total Documents --}}
        <div
            class="bg-white border border-gray-100 rounded-2xl shadow-[0_2px_15px_rgba(0,0,0,0.04)] p-5 sm:p-6">

            <div class="flex items-center gap-4 flex-wrap">

                <div
                    class="w-12 h-12 sm:w-14 sm:h-14 shrink-0 rounded-full bg-[#B70F1D]/10 flex items-center justify-center">

                    <i class="fa-solid fa-file text-[#B70F1D] text-xl sm:text-2xl"></i>

                </div>

                <div class="min-w-0 flex-1">

                    <p class="text-xs sm:text-sm text-gray-500">
                        Total Documents
                    </p>

                    <h3 class="mt-1 text-lg sm:text-xl font-semibold text-gray-800">
                        {{ $totalDocuments }}
                    </h3>

                </div>

            </div>

        </div>

    </div>


    {{-- =========================================================
        DIRECTORY LISTING
    ========================================================== --}}
    <div 
        class="mx-3 min-[390px]:mx-4 sm:mx-6 lg:mx-8 min-[1280px]:mx-10 min-[1440px]:mx-12 min-[1920px]:mx-14 my-4 sm:my-6 bg-white border border-gray-100 rounded-2xl shadow-[0_2px_20px_rgba(0,0,0,0.3)] overflow-hidden max-w-full">

        <div id="directoryCards"
            class="grid grid-cols-1 min-[480px]:grid-cols-2 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 min-[1440px]:grid-cols-5 min-[1920px]:grid-cols-6 gap-3 p-3 min-[390px]:p-4 min-[1920px]:p-5">

            @include('admin.files.partials')

        </div>


        {{-- Pagination --}}
        <div
    id="directoryPagination"
    class="px-4 sm:px-6 py-4 border-t border-gray-100 {{ $directories->hasPages() ? '' : 'hidden' }}">

    {{ $directories->links() }}

</div>

    </div>


    @push('scripts')

    <script>

    document.addEventListener('DOMContentLoaded', function () {

        let timer;

        function loadDirectories() {

            $.ajax({
                url: "{{ route('admin.dashboard') }}",
                type: "GET",

                data: {
                    search: $('#searchInput').val(),
                    type: $('#typeFilterInput').val()
                },

                success: function (response) {

                    console.log(response);

                    $('#directoryCards').html(response.html);

                    $('#directoryPagination').html(
                        response.pagination
                    );

                    if (
                typeof window.startReelProgressPolling ===
                'function'
            ) {
                window.startReelProgressPolling();
            }
                }
            });
        }


        $('#searchInput').on('keyup', function () {

            clearTimeout(timer);

            timer = setTimeout(function () {
                loadDirectories();
            }, 300);

        });


        $('.filter-option').on('click', function () {

            $('#typeFilterInput').val(
                $(this).data('filter')
            );

            loadDirectories();

        });

        $('#directoryPagination').on('click', 'a', function (event) {

    event.preventDefault();

    loadDirectories(
        $(this).attr('href')
    );

});

    });

</script>

        <script>

            document.addEventListener("DOMContentLoaded", () => {

                const filterWrapper =
                    document.getElementById("filterWrapper");

                const filterButton =
                    document.getElementById("filterButton");

                const filterDropdown =
                    document.getElementById("filterDropdown");

                const filterArrow =
                    document.getElementById("filterArrow");

                

if (
    !filterWrapper ||
    !filterButton ||
    !filterDropdown ||
    !filterArrow
) {
    return;
}

                filterButton.addEventListener("click", (event) => {

                    event.stopPropagation();

                    const isHidden =
                        filterDropdown.classList.contains("hidden");

                    if (isHidden) {

                        filterDropdown.classList.remove("hidden");

                        filterButton.setAttribute(
                            "aria-expanded",
                            "true"
                        );

                        filterArrow.classList.add(
                            "rotate-180"
                        );

                    } else {

                        closeFilter();

                    }

                });



                document.addEventListener("click", (event) => {

                    if (
                        !filterWrapper.contains(
                            event.target
                        )
                    ) {
                        closeFilter();
                    }

                });


                filterDropdown.addEventListener("click", (event) => {
                    event.stopPropagation();
                });


                function closeFilter() {

                    filterDropdown.classList.add("hidden");

                    filterButton.setAttribute(
                        "aria-expanded",
                        "false"
                    );

                    filterArrow.classList.remove(
                        "rotate-180"
                    );

                }

                /*
|--------------------------------------------------------------------------
| Reel Progress
|--------------------------------------------------------------------------
*/
function updateReelProgress(progressBox) {
    const url = progressBox.dataset.progressUrl;
    const directoryId = progressBox.id.replace(
        'reel-progress-',
        ''
    );

    const progressLabel = document.getElementById(
        'reel-progress-label-' + directoryId
    );

    const progressBar = document.getElementById(
        'reel-progress-bar-' + directoryId
    );

    if (
        !url ||
        !progressLabel ||
        !progressBar
    ) {
        return;
    }

    $.ajax({
        url: url,
        type: 'GET',
        headers: {
            Accept: 'application/json'
        },

        success: function (response) {

            if (
                !response ||
                !response.status
            ) {
                return;
            }

            /*
            |--------------------------------------------------------------------------
            | READY
            |--------------------------------------------------------------------------
            */

            if (response.ready) {

                progressLabel.textContent =
                    'Ready 100%';

                progressBar.style.width =
                    '100%';

                progressLabel.classList.remove(
                    'text-[#B70F1D]',
                    'text-red-600'
                );

                progressLabel.classList.add(
                    'text-green-600'
                );

                progressBar.classList.remove(
                    'bg-[#B70F1D]',
                    'bg-red-500'
                );

                progressBar.classList.add(
                    'bg-green-600'
                );

                /*
                | Keep 100% visible briefly,
                | then remove the complete progress box.
                */

                setTimeout(function () {
                    progressBox.remove();
                }, 800);

                return;
            }

            /*
            |--------------------------------------------------------------------------
            | FAILED
            |--------------------------------------------------------------------------
            */

            if (response.failed) {

                progressLabel.textContent =
                    'Failed';

                progressLabel.classList.remove(
                    'text-[#B70F1D]',
                    'text-green-600'
                );

                progressLabel.classList.add(
                    'text-red-600'
                );

                progressBar.classList.remove(
                    'bg-[#B70F1D]',
                    'bg-green-600'
                );

                progressBar.classList.add(
                    'bg-red-500'
                );

                return;
            }

            /*
            |--------------------------------------------------------------------------
            | PROCESSING
            |--------------------------------------------------------------------------
            */

            if (response.processing) {

    const progress = Math.max(
        0,
        Math.min(
            100,
            Number(response.progress || 0)
        )
    );

    progressLabel.textContent =
        'Processing ' + progress + '%';

    progressBar.style.width =
        progress + '%';

    progressLabel.classList.remove(
        'text-green-600',
        'text-red-600'
    );

    progressLabel.classList.add(
        'text-[#B70F1D]'
    );

    progressBar.classList.remove(
        'bg-green-600',
        'bg-red-500'
    );

    progressBar.classList.add(
        'bg-[#B70F1D]'
    );
}
        },

        error: function () {
            // Ignore temporary polling failures.
        }
    });
}


function startReelProgressPolling() {

    const progressBoxes =
        document.querySelectorAll(
            '[id^="reel-progress-"]'
        );

    if (!progressBoxes.length) {
        return;
    }

    /*
    |--------------------------------------------------------------------------
    | Local visual progress state
    |--------------------------------------------------------------------------
    */

    progressBoxes.forEach(function (progressBox) {

        progressBox.dataset.progress =
    progressBox.dataset.progress || '0';

        const bar =
            progressBox.querySelector(
                '[id^="reel-progress-bar-"]'
            );

        const label =
            progressBox.querySelector(
                '[id^="reel-progress-label-"]'
            );

        if (!bar || !label) {
            return;
        }

        let progress = parseInt(
    progressBox.dataset.progress,
    10
) || 0;

        /*
        |--------------------------------------------------------------------------
        | Smooth 1% → 99%
        |--------------------------------------------------------------------------
        */

        /*
        |--------------------------------------------------------------------------
        | Check actual Bunny/local status
        |--------------------------------------------------------------------------
        */

        

        updateReelProgress(progressBox);

        progressBox._pollingTimer =
            setInterval(function () {

                /*
                | Box may already be removed after ready.
                */

                if (!document.body.contains(progressBox)) {
                    clearInterval(
                        progressBox._pollingTimer
                    );

                    return;
                }

                updateReelProgress(
                    progressBox
                );

            }, 3000);
    });
}


startReelProgressPolling();

window.startReelProgressPolling =
    startReelProgressPolling;

/*
|--------------------------------------------------------------------------
| Delete Media Directory
|--------------------------------------------------------------------------
*/

window.deleteItem = async function (
    button,
    directoryId
) {

    if (!button || !directoryId) {
        return;
    }

    /*
    |--------------------------------------------------------------------------
    | Confirmation
    |--------------------------------------------------------------------------
    */

    const confirmed = window.confirm(
        'Are you sure you want to delete this media directory? All files inside it will also be permanently deleted.'
    );

    if (!confirmed) {
        return;
    }

    /*
    |--------------------------------------------------------------------------
    | Prevent double submission
    |--------------------------------------------------------------------------
    */

    if (button.dataset.deleting === '1') {
        return;
    }

    button.dataset.deleting = '1';

    const originalHtml =
        button.innerHTML;

    button.disabled = true;

    button.innerHTML = `
        <i class="fa-solid fa-spinner fa-spin text-xs"></i>
        <span class="hidden min-[360px]:inline">
            Deleting...
        </span>
    `;

    try {

        const response = await fetch(
            `{{ url('/admin/media') }}/${directoryId}`,
            {
                method: 'DELETE',

                headers: {
                    'X-CSRF-TOKEN':
                        '{{ csrf_token() }}',

                    'Accept':
                        'application/json',

                    'X-Requested-With':
                        'XMLHttpRequest'
                }
            }
        );

        const data =
            await response.json();

        if (!response.ok) {
            throw new Error(
                data?.message ||
                'Unable to delete media directory.'
            );
        }

        if (
            data?.status &&
            data?.redirect
        ) {
            window.location.href =
                data.redirect;

            return;
        }

        throw new Error(
            data?.message ||
            'Unable to delete media directory.'
        );

    } catch (error) {

        console.error(
            'Media directory delete error:',
            error
        );

        button.disabled = false;

        button.dataset.deleting = '0';

        button.innerHTML =
            originalHtml;

        if (
            typeof toastr !== 'undefined'
        ) {

            toastr.error(
                error?.message ||
                'Unable to delete media directory.'
            );

        } else {

            alert(
                error?.message ||
                'Unable to delete media directory.'
            );
        }
    }
};

            });

            

        </script>

    @endpush

@endsection