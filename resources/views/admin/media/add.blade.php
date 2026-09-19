@extends('layout.admin.app')

@section('content')

    <div class="flex justify-between mb-6 sm:my-8 bg-white p-4">
        <div>
            <h1 class="text-xl sm:text-2xl font-bold text-[#B70F1D] leading-none">Add Media</h1>
            <p class="text-xs sm:text-sm text-gray-400 mt-1"> Create a media directory and upload your files</p>
        </div>
    </div>

    {{-- Error --}}
    @if ($errors->any())
        <div class="mb-5 rounded-lg bg-red-50 border border-red-200 px-4 py-3 text-sm text-red-600">
            {{ $errors->first() }}
        </div>
    @endif

    <form class="flex flex-col gap-6 sm:gap-7" id="mediaForm" method="POST" action="{{ route('admin.media.store') }}"
        enctype="multipart/form-data">
        @csrf

        <div
            class="px-4 sm:px-6 lg:px-8 mx-4 sm:mx-6 lg:mx-8 bg-white border border-gray-100 shadow-[0_2px_20px_rgba(0,0,0,0.05)] rounded-2xl p-5 sm:p-7 lg:p-8 ">
            <div>
                <label for="fileName" class="block text-xs sm:text-sm font-semibold text-[#B70F1D] mb-2">Directory
                    Name</label>
                <input id="directory_name" name="directory_name" type="text" value="{{ old('directory_name') }}"
                    placeholder="Enter directory name" maxlength="255"
                    class="w-full rounded-lg border border-[#B70F1D]/40 focus:border-[#B70F1D] focus:ring-2 focus:ring-[#B70F1D]/10 outline-none px-4 py-2.5 sm:py-3 text-sm text-black transition-all" />
                <div class="error-msg directory_name_error"></div>
            </div>

            <div>
                <label for="fileType" class="block text-xs sm:text-sm font-semibold text-[#B70F1D] mb-2">
                    File Type</label>
                <select name="type" id="mediaType" required
                    class="w-full rounded-lg border border-[#B70F1D]/40 focus:border-[#B70F1D] focus:ring-2 focus:ring-[#B70F1D]/10 outline-none px-4 py-2.5 sm:py-3 text-sm text-black transition-all">
                    <option value="">Select File Type</option>

                    <option value="image" {{ old('type', $selectedType) === 'image' ? 'selected' : '' }}>
                        Image
                    </option>

                    <option value="reel" {{ old('type', $selectedType) === 'reel' ? 'selected' : '' }}>
                        Reel
                    </option>

                    <option value="document" {{ old('type', $selectedType) === 'document' ? 'selected' : '' }}>
                        Document
                    </option>
                </select>
                <div class="error-msg type_error"></div>
            </div>

            <div>
                <label for="description"
                    class="block text-xs sm:text-sm font-semibold text-[#B70F1D] mb-2">Description</label>
                <textarea id="description" name="description" rows="4" placeholder="Enter directory description"
                    class="w-full resize-none rounded-lg border border-[#B70F1D]/40 focus:border-[#B70F1D] focus:ring-2 focus:ring-[#B70F1D]/10 outline-none px-4 py-3 text-sm text-black transition-all">{{ old('description') }}</textarea>
                <div class="error-msg description_error"></div>
            </div>


            <div id="reelsUploadSection">
                <span class="block text-xs sm:text-sm font-semibold text-[#B70F1D] mb-2">Upload Reels
                </span>

                <label for="uploadReels"
                    class="flex flex-col items-center justify-center text-center border-2 border-dashed border-[#B70F1D]/30 rounded-xl bg-[#B70F1D]/[0.02] hover:bg-[#B70F1D]/[0.04] transition-colors cursor-pointer py-10 sm:py-14 px-4">
                    <input id="mediaFiles" name="files[]" type="file" multiple class="hidden" />
                    <div
                        class="w-11 h-11 sm:w-12 sm:h-12 mb-3 sm:mb-4 flex items-center justify-center text-[#B70F1D] text-[42px] sm:text-[50px]">
                        <i class="fa-solid fa-upload"></i>
                    </div>
                    <p id="uploadHint" class="text-sm sm:text-[15px] text-gray-700 font-medium">Please select a file
                        type first

                    </p>

                    <button type="button" disabled id="chooseFilesBtn"
                        class="text-sm sm:text-[15px] text-gray-700 font-medium">Click to upload or drag and
                        drop
                    </button>
                    <p class="text-xs sm:text-[13px] text-gray-400 mt-1.5">Supported formats: MP4</p>
                </label>
                <div class="error-msg files_error"></div>
            </div>

            {{-- Selected Files --}}
            <div id="selectedFilesWrapper" class="hidden mt-4">
                <div class="flex items-center justify-between mb-2">
                    <h3 class="text-sm font-semibold text-gray-700">
                        Selected Files
                    </h3>

                    <span id="fileCount" class="text-xs text-gray-400">
                        0 files
                    </span>
                </div>

                <div id="selectedFiles" class="space-y-2"></div>
            </div>

            <div class="pt-1">
                <button type="button" id="submitBtn" onclick="submitMedia()"
                    class="inline-flex items-center justify-center gap-2 w-full sm:w-auto bg-[#B70F1D] hover:bg-[#7A0F17] active:bg-[#650C13] text-white text-sm font-medium rounded-lg px-5 py-2.5 sm:px-6 sm:py-3 transition-colors">
                    <i class="fa-solid fa-plus"></i>
                    <span id="submitText">
                        Upload Media
                    </span>
                    <span id="submitSpinner" class="hidden">
                        <i class="fa-solid fa-spinner fa-spin"></i>
                    </span>
                </button>
            </div>
        </div>
    </form>

@endsection


@push('scripts')

    <script>
        document.addEventListener('DOMContentLoaded', function () {

            /*
            |--------------------------------------------------------------------------
            | Elements
            |--------------------------------------------------------------------------
            */

            const form = document.getElementById('mediaForm');
            const directoryName = document.getElementById('directory_name');
            const mediaType = document.getElementById('mediaType');
            const description = document.getElementById('description');
            const mediaFiles = document.getElementById('mediaFiles');

            const chooseFilesBtn = document.getElementById('chooseFilesBtn');
            const uploadHint = document.getElementById('uploadHint');

            const selectedFilesWrapper =
                document.getElementById('selectedFilesWrapper');

            const selectedFilesContainer =
                document.getElementById('selectedFiles');

            const fileCount =
                document.getElementById('fileCount');

            const submitBtn =
                document.getElementById('submitBtn');

            const submitText =
                document.getElementById('submitText');

            const submitSpinner =
                document.getElementById('submitSpinner');


            /*
            |--------------------------------------------------------------------------
            | State
            |--------------------------------------------------------------------------
            */

            let selectedFiles = [];

            let directoryNameValidated = false;

            let directoryNameRequest = null;


            /*
            |--------------------------------------------------------------------------
            | File Rules
            |--------------------------------------------------------------------------
            */

            const mediaFileRules = {
                image: {
                    extensions: ['jpg', 'jpeg', 'png', 'webp'],
                    accept: '.jpg,.jpeg,.png,.webp',
                    maxSize: 10 * 1024 * 1024,
                    maxFiles: 10,
                    label: 'JPG, JPEG, PNG or WEBP — max 10 MB per file'
                },

                reel: {
                    extensions: ['mp4'],
                    accept: '.mp4',
                    maxSize: 50 * 1024 * 1024,
                    maxFiles: 5,
                    label: 'MP4 — max 50 MB per file'
                },

                document: {
                    extensions: ['pdf', 'doc', 'docx'],
                    accept: '.pdf,.doc,.docx',
                    maxSize: 50 * 1024 * 1024,
                    maxFiles: 10,
                    label: 'PDF, DOC or DOCX — max 50 MB per file'
                }
            };


            /*
            |--------------------------------------------------------------------------
            | Common Validation Helpers
            |--------------------------------------------------------------------------
            */

            function showFieldError(field, message) {
                $('.' + field + '_error').text(message);
            }


            function clearFieldError(field) {
                $('.' + field + '_error').text('');
            }


            function clearAllErrors() {
                document
                    .querySelectorAll('.error-msg')
                    .forEach(function (element) {
                        element.innerText = '';
                    });
            }


            /*
            |--------------------------------------------------------------------------
            | Get Current File Rule
            |--------------------------------------------------------------------------
            */

            function getCurrentFileRule() {
                return mediaFileRules[mediaType.value] ?? null;
            }


            /*
            |--------------------------------------------------------------------------
            | Directory Name Validation
            |--------------------------------------------------------------------------
            */

            async function validateDirectoryName() {

                const name = directoryName.value.trim();


                if (!name) {

                    showFieldError(
                        'directory_name',
                        'Directory name is required.'
                    );

                    directoryNameValidated = false;

                    return false;
                }


                if (name.length > 255) {

                    showFieldError(
                        'directory_name',
                        'Directory name may not be greater than 255 characters.'
                    );

                    directoryNameValidated = false;

                    return false;
                }


                const pattern = /^[a-zA-Z0-9 _-]+$/;


                if (!pattern.test(name)) {

                    showFieldError(
                        'directory_name',
                        'Directory name may only contain letters, numbers, spaces, underscores and hyphens.'
                    );

                    directoryNameValidated = false;

                    return false;
                }


                /*
                | If this exact name was already successfully checked,
                | avoid another AJAX request.
                */

                if (
                    directoryNameValidated &&
                    directoryName.dataset.validatedName === name
                ) {
                    return true;
                }


                /*
                | Cancel previous request if user submits/blurred quickly.
                */

                if (directoryNameRequest) {
                    directoryNameRequest.abort();
                }


                const controller = new AbortController();

                directoryNameRequest = controller;


                try {

                    const response = await fetch(
                        "{{ route('admin.media.check-name') }}",
                        {
                            method: 'POST',

                            headers: {
                                'X-CSRF-TOKEN':
                                    "{{ csrf_token() }}",

                                'Accept':
                                    'application/json',

                                'Content-Type':
                                    'application/json',

                                'X-Requested-With':
                                    'XMLHttpRequest'
                            },

                            body: JSON.stringify({
                                name: name
                            }),

                            signal: controller.signal
                        }
                    );


                    const data = await response.json();


                    if (!response.ok) {
                        throw data;
                    }


                    if (data.exists) {

                        showFieldError(
                            'directory_name',
                            'This directory name already exists.'
                        );

                        directoryNameValidated = false;

                        delete directoryName.dataset.validatedName;

                        return false;
                    }


                    clearFieldError('directory_name');

                    directoryNameValidated = true;

                    directoryName.dataset.validatedName = name;

                    return true;


                } catch (error) {

                    /*
                    | Ignore intentionally cancelled request.
                    */

                    if (error.name === 'AbortError') {
                        return false;
                    }


                    showFieldError(
                        'directory_name',
                        error.message ||
                        'Unable to validate directory name.'
                    );

                    directoryNameValidated = false;

                    delete directoryName.dataset.validatedName;

                    return false;


                } finally {

                    if (directoryNameRequest === controller) {
                        directoryNameRequest = null;
                    }
                }
            }


            /*
            |--------------------------------------------------------------------------
            | File Type Validation
            |--------------------------------------------------------------------------
            */

            function validateType() {

                const type = mediaType.value;


                if (!type) {

                    showFieldError(
                        'type',
                        'Please select a file type.'
                    );

                    return false;
                }


                if (!Object.prototype.hasOwnProperty.call(
                    mediaFileRules,
                    type
                )) {

                    showFieldError(
                        'type',
                        'Please select a valid file type.'
                    );

                    return false;
                }


                clearFieldError('type');

                return true;
            }


            /*
            |--------------------------------------------------------------------------
            | Description Validation
            |--------------------------------------------------------------------------
            */

            function validateDescription() {

                const value =
                    description.value.trim();


                if (!value) {

                    showFieldError(
                        'description',
                        'Description is required.'
                    );

                    return false;
                }


                if (value.length > 1000) {

                    showFieldError(
                        'description',
                        'Description may not be greater than 1000 characters.'
                    );

                    return false;
                }


                clearFieldError('description');

                return true;
            }


            /*
            |--------------------------------------------------------------------------
            | Files Validation
            |--------------------------------------------------------------------------
            */

            function validateFiles(files = null) {

                const selected =
                    files ?? Array.from(mediaFiles.files);

                const type =
                    mediaType.value;

                const rule =
                    mediaFileRules[type];


                if (!selected.length) {

                    showFieldError(
                        'files',
                        'Please select at least one file.'
                    );

                    return false;
                }


                if (!rule) {

                    showFieldError(
                        'files',
                        'Please select a valid file type first.'
                    );

                    return false;
                }


                if (selected.length > rule.maxFiles) {

                    showFieldError(
                        'files',
                        `You can upload maximum ${rule.maxFiles} files.`
                    );

                    return false;
                }


                const fileNames = new Set();


                for (const file of selected) {

                    const extension =
                        file.name
                            .split('.')
                            .pop()
                            .toLowerCase();


                    if (!rule.extensions.includes(extension)) {

                        showFieldError(
                            'files',
                            `${file.name} is not a valid ${type} file.`
                        );

                        return false;
                    }


                    if (file.size > rule.maxSize) {

                        showFieldError(
                            'files',
                            `${file.name} exceeds the maximum allowed file size.`
                        );

                        return false;
                    }


                    const fileName =
                        file.name.toLowerCase();


                    if (fileNames.has(fileName)) {

                        showFieldError(
                            'files',
                            `${file.name} is selected more than once.`
                        );

                        return false;
                    }


                    fileNames.add(fileName);
                }


                clearFieldError('files');

                return true;
            }


            /*
            |--------------------------------------------------------------------------
            | Update Upload State
            |--------------------------------------------------------------------------
            */

            function updateUploadState() {

                const rule =
                    getCurrentFileRule();


                /*
                | Type changed = previously selected files are no longer valid.
                */

                selectedFiles = [];

                mediaFiles.value = '';

                renderSelectedFiles();

                clearFieldError('files');


                if (!rule) {

                    chooseFilesBtn.disabled = true;

                    uploadHint.textContent =
                        'Please select a file type first';

                    updateSubmitState();

                    return;
                }


                chooseFilesBtn.disabled = false;

                mediaFiles.accept =
                    rule.accept;

                uploadHint.textContent =
                    `${rule.label} • Maximum ${rule.maxFiles} files`;


                updateSubmitState();
            }


            /*
            |--------------------------------------------------------------------------
            | Sync Selected Files With Input
            |--------------------------------------------------------------------------
            */

            function syncInputFiles() {

                const dataTransfer =
                    new DataTransfer();


                selectedFiles.forEach(function (file) {

                    dataTransfer.items.add(file);

                });


                mediaFiles.files =
                    dataTransfer.files;
            }


            /*
            |--------------------------------------------------------------------------
            | Render Selected Files
            |--------------------------------------------------------------------------
            */

function renderSelectedFiles() {

    selectedFilesContainer.innerHTML = '';

    if (!selectedFiles.length) {
        selectedFilesWrapper.classList.add('hidden');
        fileCount.textContent = '0 files';
        return;
    }

    selectedFilesWrapper.classList.remove('hidden');

    fileCount.textContent =
        `${selectedFiles.length} file${selectedFiles.length > 1 ? 's' : ''}`;

    selectedFiles.forEach(function (file, index) {

        const row = document.createElement('div');

        row.className =
            'flex items-center justify-between gap-3 rounded-lg border border-gray-200 bg-gray-50 px-3 py-2.5';

        const sizeMB =
            (file.size / (1024 * 1024)).toFixed(2);

        let previewHtml = '';

        /*
        |--------------------------------------------------------------------------
        | Image Preview
        |--------------------------------------------------------------------------
        */

        if (mediaType.value === 'image') {

            const previewUrl =
                URL.createObjectURL(file);

            previewHtml = `
                <img
                    src="${previewUrl}"
                    alt="${escapeHtml(file.name)}"
                    class="w-12 h-12 rounded-lg object-cover border border-gray-200 shrink-0"
                >
            `;

        /*
        |--------------------------------------------------------------------------
        | Reel Preview
        |--------------------------------------------------------------------------
        */

        } else if (mediaType.value === 'reel') {

            const previewUrl =
                URL.createObjectURL(file);

            previewHtml = `
                <video
                    src="${previewUrl}"
                    class="w-12 h-12 rounded-lg object-cover border border-gray-200 shrink-0"
                    muted
                    autoplay
                    loop
                    playsinline
                    preload="metadata">
                </video>
            `;

        /*
        |--------------------------------------------------------------------------
        | Document Preview
        |--------------------------------------------------------------------------
        */

        } else {

            previewHtml = `
                <div
                    class="w-12 h-12 rounded-lg bg-gray-100 flex items-center justify-center text-[#B70F1D] shrink-0">
                    <i class="fa-solid fa-file text-xl"></i>
                </div>
            `;
        }

        row.innerHTML = `
            <div class="min-w-0 flex items-center gap-3">

                ${previewHtml}

                <div class="min-w-0">

                    <p class="truncate text-sm font-medium text-gray-700">
                        ${escapeHtml(file.name)}
                    </p>

                    <p class="text-xs text-gray-400">
                        ${sizeMB} MB
                    </p>

                </div>

            </div>

            <button
                type="button"
                data-index="${index}"
                class="remove-file shrink-0 text-red-500 hover:text-red-700"
            >
                <i class="fa-solid fa-xmark"></i>
            </button>
        `;

        selectedFilesContainer.appendChild(row);
    });
}
            /*
            |--------------------------------------------------------------------------
            | Submit Button State
            |--------------------------------------------------------------------------
            */

            function updateSubmitState() {
                // Submit button should remain clickable so validation
                // can run and focus the first invalid field.
                submitBtn.disabled = false;
            }


            /*
            |--------------------------------------------------------------------------
            | Directory Name - Blur
            |--------------------------------------------------------------------------
            */

            directoryName.addEventListener(
                'blur',
                validateDirectoryName
            );


            /*
            |--------------------------------------------------------------------------
            | Directory Name - Input
            |--------------------------------------------------------------------------
            */

            directoryName.addEventListener(
                'input',
                function () {

                    directoryNameValidated = false;

                    delete directoryName.dataset.validatedName;

                    clearFieldError('directory_name');

                    updateSubmitState();
                }
            );


            /*
            |--------------------------------------------------------------------------
            | File Type - Change
            |--------------------------------------------------------------------------
            */

            mediaType.addEventListener(
                'change',
                function () {

                    validateType();

                    updateUploadState();
                }
            );


            /*
            |--------------------------------------------------------------------------
            | Description - Blur
            |--------------------------------------------------------------------------
            */

            description.addEventListener(
                'blur',
                validateDescription
            );


            /*
            |--------------------------------------------------------------------------
            | Description - Input
            |--------------------------------------------------------------------------
            */

            description.addEventListener(
                'input',
                function () {

                    clearFieldError('description');

                    updateSubmitState();
                }
            );


            /*
            |--------------------------------------------------------------------------
            | Files - Change
            |--------------------------------------------------------------------------
            */

            mediaFiles.addEventListener(
                'change',
                function (event) {

                    const files =
                        Array.from(event.target.files);


                    if (!validateFiles(files)) {

                        selectedFiles = [];

                        mediaFiles.value = '';

                        renderSelectedFiles();

                        updateSubmitState();

                        return;
                    }


                    selectedFiles = files;

                    syncInputFiles();

                    renderSelectedFiles();

                    clearFieldError('files');

                    updateSubmitState();
                }
            );


            /*
            |--------------------------------------------------------------------------
            | Choose Files
            |--------------------------------------------------------------------------
            */

            chooseFilesBtn.addEventListener(
                'click',
                function () {

                    if (!getCurrentFileRule()) {
                        return;
                    }

                    mediaFiles.click();
                }
            );


            /*
            |--------------------------------------------------------------------------
            | Remove Selected File
            |--------------------------------------------------------------------------
            */

            selectedFilesContainer.addEventListener(
                'click',
                function (event) {

                    const button =
                        event.target.closest('.remove-file');


                    if (!button) {
                        return;
                    }


                    const index =
                        Number(button.dataset.index);


                    selectedFiles.splice(
                        index,
                        1
                    );


                    syncInputFiles();

                    renderSelectedFiles();

                    updateSubmitState();


                    if (selectedFiles.length) {

                        validateFiles();

                    } else {

                        showFieldError(
                            'files',
                            'Please select at least one file.'
                        );
                    }
                }
            );


            /*
            |--------------------------------------------------------------------------
            | Submit Media
            |--------------------------------------------------------------------------
            */

            window.submitMedia = async function () {

                /*
                |--------------------------------------------------------------------------
                | Prevent double submission
                |--------------------------------------------------------------------------
                */
                if (submitBtn.disabled) {
                    return;
                }

                /*
                |--------------------------------------------------------------------------
                | Clear previous errors
                |--------------------------------------------------------------------------
                */
                clearAllErrors();

                /*
                |--------------------------------------------------------------------------
                | Frontend Validation
                |--------------------------------------------------------------------------
                */
                const validDirectoryName =
                    await validateDirectoryName();

                const validType =
                    validateType();

                const validDescription =
                    validateDescription();

                const validFiles =
                    validateFiles();

                /*
                |--------------------------------------------------------------------------
                | Stop submission if validation fails
                |--------------------------------------------------------------------------
                */
                if (
                    !validDirectoryName ||
                    !validType ||
                    !validDescription ||
                    !validFiles
                ) {

                    /*
                    | Reset button state
                    */
                    submitBtn.disabled = false;

                    submitText.textContent = 'Upload Media';

                    submitSpinner.classList.add('hidden');

                    /*
                    |--------------------------------------------------------------------------
                    | Focus first invalid field
                    |--------------------------------------------------------------------------
                    */
                    if (!validDirectoryName) {

                        directoryName.focus();

                    } else if (!validType) {

                        mediaType.focus();

                    } else if (!validDescription) {

                        description.focus();

                    } else if (!validFiles) {

                        chooseFilesBtn.focus();
                    }

                    return;
                }

                /*
                |--------------------------------------------------------------------------
                | Disable UI only after frontend validation succeeds
                |--------------------------------------------------------------------------
                */
                /*
    |--------------------------------------------------------------------------
    | Prepare UI state
    |--------------------------------------------------------------------------
    */
                submitBtn.disabled = true;

                submitText.textContent = 'Processing...';

                submitSpinner.classList.remove('hidden');

                try {

                    /*
                    |--------------------------------------------------------------------------
                    | Prepare FormData BEFORE disabling form controls
                    |--------------------------------------------------------------------------
                    */
                    const formData = new FormData(form);

                    /*
                    |--------------------------------------------------------------------------
                    | Disable UI after FormData has been created
                    |--------------------------------------------------------------------------
                    */
                    chooseFilesBtn.disabled = true;

                    mediaType.disabled = true;

                    directoryName.disabled = true;

                    description.disabled = true;

                    /*
                    |--------------------------------------------------------------------------
                    | Submit
                    |--------------------------------------------------------------------------
                    */
                    const response = await fetch(
                        form.action,
                        {
                            method: 'POST',

                            headers: {
                                'X-CSRF-TOKEN':
                                    "{{ csrf_token() }}",

                                'Accept':
                                    'application/json',

                                'X-Requested-With':
                                    'XMLHttpRequest'
                            },

                            body: formData
                        }
                    );

                    /*
                    |--------------------------------------------------------------------------
                    | Safely parse response
                    |--------------------------------------------------------------------------
                    */
                    const contentType =
                        response.headers.get('content-type') || '';

                    let data = null;

                    if (contentType.includes('application/json')) {

                        data = await response.json();

                    } else {

                        const text = await response.text();

                        console.error(
                            'Unexpected server response:',
                            text
                        );

                        throw {
                            message:
                                `Server returned an unexpected response (${response.status}).`
                        };
                    }

                    /*
                    |--------------------------------------------------------------------------
                    | Laravel validation / server error
                    |--------------------------------------------------------------------------
                    */
                    if (!response.ok) {
                        throw data;
                    }

                    /*
                    |--------------------------------------------------------------------------
                    | Success
                    |--------------------------------------------------------------------------
                    */
                    if (
                        data?.status &&
                        data?.redirect
                    ) {

                        window.location.href =
                            data.redirect;

                        return;
                    }

                    throw {
                        message:
                            data?.message ||
                            'Unable to upload media. Please try again.'
                    };

                } catch (error) {

                    console.error(
                        'Media upload error:',
                        error
                    );

                    clearAllErrors();

                    /*
                    |--------------------------------------------------------------------------
                    | Laravel Validation Errors
                    |--------------------------------------------------------------------------
                    */
                    if (
                        error?.errors &&
                        typeof error.errors === 'object'
                    ) {

                        let firstErrorField = null;

                        Object.entries(error.errors)
                            .forEach(function ([field, messages]) {

                                const errorField =
                                    field === 'files' ||
                                        field.startsWith('files.')
                                        ? 'files'
                                        : field;

                                const message =
                                    Array.isArray(messages)
                                        ? messages[0]
                                        : messages;

                                if (!message) {
                                    return;
                                }

                                showFieldError(
                                    errorField,
                                    message
                                );

                                if (!firstErrorField) {
                                    firstErrorField =
                                        errorField;
                                }
                            });

                        /*
                        |--------------------------------------------------------------------------
                        | Focus first backend validation error
                        |--------------------------------------------------------------------------
                        */
                        if (
                            firstErrorField ===
                            'directory_name'
                        ) {

                            directoryName.focus();

                        } else if (
                            firstErrorField === 'type'
                        ) {

                            mediaType.focus();

                        } else if (
                            firstErrorField === 'description'
                        ) {

                            description.focus();

                        } else if (
                            firstErrorField === 'files'
                        ) {

                            chooseFilesBtn.focus();
                        }

                    } else {

                        /*
                        |--------------------------------------------------------------------------
                        | Non-validation error
                        |--------------------------------------------------------------------------
                        */
                        console.error(
                            error?.message ||
                            'Unable to upload media.'
                        );

                        if (typeof toastr !== 'undefined') {

                            toastr.error(
                                error?.message ||
                                'Unable to upload media. Please try again.'
                            );
                        }
                    }

                } finally {

                    /*
                    |--------------------------------------------------------------------------
                    | Restore UI
                    |--------------------------------------------------------------------------
                    */
                    submitBtn.disabled = false;

                    submitText.textContent =
                        'Upload Media';

                    submitSpinner.classList.add('hidden');

                    mediaType.disabled = false;

                    directoryName.disabled = false;

                    description.disabled = false;

                    chooseFilesBtn.disabled =
                        !getCurrentFileRule();
                }
            };

            /*
            |--------------------------------------------------------------------------
            | Escape HTML
            |--------------------------------------------------------------------------
            */

            function escapeHtml(value) {

                const div =
                    document.createElement('div');

                div.textContent =
                    value;

                return div.innerHTML;
            }


            /*
            |--------------------------------------------------------------------------
            | Initial State
            |--------------------------------------------------------------------------
            */

            updateUploadState();

        });

    </script>

@endpush