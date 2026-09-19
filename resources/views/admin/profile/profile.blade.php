@extends('layout.admin.app')

@section('content')



    <div class="flex justify-between mb-6 sm:my-8 bg-white p-4">
        <div>
            <h1 class="text-xl sm:text-2xl font-bold text-[#B70F1D] leading-none">Update Profile</h1>
            <p class="text-xs sm:text-sm text-gray-400 mt-1" id="stepSubtitle">Manage your personal account information</p>
        </div>
    </div>

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
        class="mx-4 sm:mx-6 lg:mx-8 bg-white border border-gray-100 rounded-2xl shadow-[0_2px_20px_rgba(0,0,0,0.05)] overflow-hidden">
        <div id="globalSuccess" class="alert d-none"></div>

        <form id="adminForm" class="flex flex-col">
            <div class="px-4 sm:px-6 lg:px-8 py-6 sm:py-7 lg:py-8">
                <div class="grid grid-cols-1 lg:grid-cols-[260px_minmax(0,1fr)] gap-7 lg:gap-8">
                    <div class="flex flex-col items-center text-center lg:border-r lg:border-gray-200 lg:pr-8">
                        <label class="block text-xs sm:text-sm font-semibold text-gray-800 mb-3">Profile
                            Photo</label>
                        <div class="relative">
                            <div
                                class="w-24 h-24 sm:w-28 sm:h-28 rounded-full bg-gray-100 flex items-center justify-center overflow-hidden">
                                @if(Auth::user()->image && file_exists(public_path('admin_assets/images/' . Auth::user()->image)))
                                    <img id="profilePreview" src="{{ asset('admin_assets/images/' . Auth::user()->image) }}"
                                        alt="Profile Photo" class="w-full h-full object-cover" />
                                    <i id="defaultProfileIcon"
                                        class="fa-solid fa-user text-gray-500 text-4xl sm:text-5xl hidden"></i>
                                @else
                                    <img id="profilePreview" src="" alt="Profile Photo"
                                        class="hidden w-full h-full object-cover" />
                                    <i id="defaultProfileIcon" class="fa-solid fa-user text-gray-500 text-4xl sm:text-5xl"></i>
                                @endif
                            </div>
                            <label for="adminPhotoInput"
                                class="absolute right-0 bottom-0 w-8 h-8 sm:w-9 sm:h-9 rounded-full bg-[#B70F1D]/10 text-[#B70F1D] flex items-center justify-center cursor-pointer border-2 border-white hover:bg-[#B70F1D] hover:text-white transition-all duration-200"
                                aria-label="Change profile photo">
                                <i class="fa-solid fa-camera text-xs sm:text-sm"></i>
                            </label>
                        </div>
                        <input id="adminPhotoInput" name="image" type="file" accept="image/*" class="hidden"
                            onchange="previewAdminPhoto(this)" />

                        <p class="mt-3 text-[10px] sm:text-xs text-gray-400 leading-4">JPG, JPEG, PNG,
                            WEBP,<span> Max 200 × 200 pixels</span>,</p>
                        <label for="adminPhotoInput"
                            class="mt-3 inline-flex items-center justify-center gap-2 h-9 sm:h-10 px-4 sm:px-5 rounded-lg border border-[#B70F1D]/50 bg-white text-[#B70F1D] text-xs sm:text-sm font-medium cursor-pointer hover:bg-[#B70F1D]/5 transition-all duration-200">
                            <i class="fa-solid fa-upload text-xs"></i>
                            <span>Change Photo</span>
                        </label>
                        <small class="error-msg image_error"></small>
                    </div>


                    <div class="grid grid-cols-1 gap-5 sm:gap-6">
                        <div>
                            <label for="profileName" class="block text-xs sm:text-sm font-semibold text-gray-800 mb-2">Name
                                <span class="text-[#B70F1D]">*</span>
                            </label>
                            <div class="relative">
                                <span
                                    class="absolute left-0 top-0 h-full w-10 sm:w-11 flex items-center justify-center text-gray-400">
                                    <i class="fa-regular fa-user text-sm"></i>
                                </span>
                                <input id="adminName" name="name" type="text"
                                    value="{{ ucfirst(Auth::user()->name) ?? 'N/A' }}" placeholder="Enter your name"
                                    class="w-full h-10 sm:h-11 lg:h-12 rounded-lg border border-gray-200 bg-white pl-10 sm:pl-11 pr-3 sm:pr-4 text-xs sm:text-sm text-gray-800 placeholder:text-gray-400 outline-none transition-all duration-200 focus:border-[#B70F1D] focus:ring-2 focus:ring-[#B70F1D]/10" />
                                <small class="error-msg name_error"></small>
                            </div>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-5 sm:gap-6">
                            <div>
                                <label for="profileEmail" class="block text-xs sm:text-sm font-semibold text-gray-800 mb-2">
                                    Email
                                    <span class="text-[#B70F1D]">*</span>
                                </label>
                                <div class="relative">
                                    <span
                                        class="absolute left-0 top-0 h-full w-10 sm:w-11 flex items-center justify-center text-gray-400">
                                        <i class="fa-regular fa-envelope text-sm"></i>
                                    </span>
                                    <input id="adminEmail" name="email" type="text"
                                        value="{{ Auth::user()->email ?? 'N/A'  }}" placeholder="Enter your email"
                                        class="w-full h-10 sm:h-11 lg:h-12 rounded-lg border border-gray-200 bg-white pl-10 sm:pl-11 pr-3 sm:pr-4 text-xs sm:text-sm text-gray-800 placeholder:text-gray-400 outline-none transition-all duration-200 focus:border-[#B70F1D] focus:ring-2 focus:ring-[#B70F1D]/10" />
                                    <small class="error-msg email_error"></small>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
            </div>

            <div
                class="border-t border-gray-100 px-4 sm:px-6 lg:px-8 py-4 sm:py-5 flex flex-col-reverse sm:flex-row sm:items-center sm:justify-end gap-3">
                <button type="button" onclick="window.history.back()"
                    class="w-full sm:w-auto inline-flex items-center justify-center h-10 sm:h-11 px-5 sm:px-6 rounded-lg border border-gray-200 bg-white text-gray-700 text-xs sm:text-sm font-medium hover:bg-gray-50 hover:border-gray-300 transition-all duration-200">Cancel</button>
                <button type="submit" id="updateProfileBtn"
                    class="w-full sm:w-auto inline-flex items-center justify-center gap-2 h-10 sm:h-11 px-5 sm:px-6 lg:px-7 rounded-lg bg-[#B70F1D] hover:bg-[#A20D19] active:bg-[#8F0B16] text-white text-xs sm:text-sm font-semibold shadow-sm hover:shadow-md transition-all duration-200">
                    <i class="fa-regular fa-floppy-disk text-xs sm:text-sm"></i>
                    <span>Update Profile</span>
                </button>
            </div>
        </form>
    </div>

    @push('scripts')
        <script>
            window.addEventListener("DOMContentLoaded", () => {
                const successMsg = sessionStorage.getItem("successMessage");
                const errorMsg = sessionStorage.getItem("errorMessage");

                const box = document.getElementById("globalSuccess");

                if (successMsg || errorMsg) {

                    box.classList.remove("d-none");

                    if (successMsg) {
                        box.classList.remove("alert-danger");
                        box.classList.add("alert-success");
                        box.innerText = successMsg;
                        sessionStorage.removeItem("successMessage");
                    }

                    if (errorMsg) {
                        box.classList.remove("alert-success");
                        box.classList.add("alert-danger");
                        box.innerText = errorMsg;
                        sessionStorage.removeItem("errorMessage");
                    }

                    // SCROLL + FOCUS
                    setTimeout(() => {
                        box.scrollIntoView({ behavior: "smooth", block: "center" });
                    }, 100);

                    // optional auto hide
                    setTimeout(() => {
                        box.classList.add("d-none");
                    }, 4000);
                }
            });

            function showFieldError(field, message) {
                $('.' + field + '_error').text(message);
            }

            function clearFieldError(field) {
                $('.' + field + '_error').text('');
            }

            // ORGANISATION NAME
            async function validateadminName() {

                let name = $('#adminName').val().trim();
                let regex = /^[A-Za-z\s]+$/;

                if (name === '') {
                    showFieldError('name', 'Name field is required.');
                    return false;
                }

                if (!regex.test(name)) {
                    showFieldError('name', 'Name must contain letters and spaces only.');
                    return false;
                }

                clearFieldError('name');
                return true;
            }


            // EMAIL
            async function validateadminEmail() {

                let email = $('#adminEmail').val().trim();
                let emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

                if (email === '') {
                    showFieldError('email', 'Email field is required.');
                    return false;
                }

                if (!emailPattern.test(email)) {
                    showFieldError('email', 'Please enter a valid email address.');
                    return false;
                }

                try {

                    let response = await $.ajax({
                        url: "{{ route('admin.checkEmail') }}",
                        type: "POST",
                        data: {
                            _token: $('meta[name="csrf-token"]').attr('content'),
                            email: email
                        }
                    });

                    if (response.exists) {
                        showFieldError('email', 'This email address is already registered.');
                        return false;
                    }

                    clearFieldError('email');
                    return true;

                } catch (error) {

                    showFieldError('email', 'Error checking email.');
                    return false;

                }
            }


            function previewAdminPhoto(input) {

                const file = input.files[0];
                if (!file) return;

                const preview = document.getElementById("profilePreview");

                let allowed = [
                    "image/jpeg",
                    "image/jpg",
                    "image/png",
                    "image/webp"
                ];

                if (!allowed.includes(file.type)) {
                    showFieldError('image', 'Profile photo must be jpg, jpeg, png or webp.');
                    input.value = "";
                    return;
                }

                if (file.size > 5 * 1024 * 1024) {
                    showFieldError('image', 'Profile photo must be less than 5 MB.');
                    input.value = "";
                    return;
                }

                const reader = new FileReader();

                reader.onload = function (e) {

                    let img = new Image();

                    img.onload = function () {

                        if (img.width > 200 || img.height > 200) {
                            showFieldError('image', 'Profile photo must be max 200 × 200 pixels.');
                            input.value = "";
                            preview.style.display = "none";
                            return;
                        }

                        preview.src = e.target.result;
                        preview.style.display = "block";

                        $('#defaultProfileIcon').addClass('hidden');

                        clearFieldError('image');

                    };

                    img.src = e.target.result;

                };

                reader.readAsDataURL(file);

            }






            $(document).ready(function () {

                $('#adminName').on('blur', validateadminName);

                $('#adminEmail').on('blur', validateadminEmail);


            });

            $('#adminForm').on('submit', async function (e) {

                e.preventDefault();

                let formData = new FormData();

                $('.error-msg').text('');

                const validName = await validateadminName();
                const validEmail = await validateadminEmail();

                if (!validName || !validEmail) {
                    return;
                }

                formData.append('_token', $('meta[name="csrf-token"]').attr('content'));
                formData.append('name', $('#adminName').val());
                formData.append('email', $('#adminEmail').val());

                let image = $('#adminPhotoInput')[0].files[0];
                if (image) {
                    formData.append('image', image);
                }

                $.ajax({
                    url: "{{ route('admin.profile.update') }}",
                    type: "POST",
                    data: formData,
                    processData: false,
                    contentType: false,

                    beforeSend: function () {
                        $('#updateProfileBtn')
                            .prop('disabled', true)
                            .html('Processing...');
                    },

                    success: function (response) {

                        $('#updateProfileBtn')
                            .prop('disabled', false)
                            .html('Update Profile <i class="bx bx-check"></i>');

                        sessionStorage.setItem("successMessage", response.message);
                        window.location.href = response.redirect;

                    },
                    error: function (xhr) {

                        $('#updateProfileBtn')
                            .prop('disabled', false)
                            .html('Update Profile <i class="bx bx-check"></i>');

                        $('.error-msg').text('');

                        if (xhr.status == 422) {

                            $.each(xhr.responseJSON.errors, function (key, value) {
                                $('.' + key + '_error').text(value[0]);
                            });

                        } else {

                            alert(xhr.responseJSON.message);

                        }
                    }
                });

            });
        </script>
    @endpush
@endsection