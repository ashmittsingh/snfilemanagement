@extends('layout.admin.app')

@section('content')

    <div class="flex justify-between mb-6 sm:my-8 bg-white p-4">
        <div>
            <h1 class="text-xl sm:text-2xl font-bold text-[#B70F1D] leading-none">Edit Password</h1>
            <p class="text-xs sm:text-sm text-gray-400 mt-1">Edit your password to keep your account
                secure</p>
        </div>
    </div>

    {{-- Single unified alert box. Populated either from a server flash (passed below as data
         attributes) or from sessionStorage after an AJAX submit. Only ONE of these will ever
         render at a time, so success/error messages never appear twice. --}}
    <div
        id="globalAlert"
        class="hidden mx-4 sm:mx-6 lg:mx-8 mb-4 items-start gap-3 rounded-xl border px-4 py-3 text-sm font-medium"
        data-server-success="{{ Session::get('success') ?? '' }}"
        data-server-error="{{ Session::get('error') ?? '' }}"
    >
        <i id="globalAlertIcon" class="fa-solid text-base mt-0.5"></i>
        <span id="globalAlertText" class="flex-1"></span>
        <button type="button" id="globalAlertClose" class="text-current/60 hover:text-current" aria-label="Dismiss">
            <i class="fa-solid fa-xmark text-sm"></i>
        </button>
    </div>

    <div
        class="mx-4 sm:mx-6 lg:mx-8 bg-white border border-gray-100 rounded-2xl shadow-[0_2px_20px_rgba(0,0,0,0.05)] overflow-hidden">
        <form id="passwordForm" class="flex flex-col">

            <div class="px-4 sm:px-6 lg:px-8 py-6 sm:py-7 lg:py-8">
                <div>
                    <label for="old_password" class="block text-xs sm:text-sm font-semibold text-gray-800 mb-2">
                        Current Password
                        <span class="text-[#B70F1D]">*</span>
                    </label>
                    <div class="relative">
                        <input id="old_password" name="old_password" type="password"
                            placeholder="Enter your current password"
                            class="w-full h-10 sm:h-11 lg:h-12 rounded-lg border border-gray-200 bg-white px-3 sm:px-4 pr-11 sm:pr-12 text-xs sm:text-sm text-gray-800 placeholder:text-gray-400 outline-none transition-all duration-200 focus:border-[#B70F1D] focus:ring-2 focus:ring-[#B70F1D]/10" />

                        <button type="button" data-toggle-password="old_password"
                            class="js-toggle-password absolute right-0 top-0 h-full w-10 sm:w-12 flex items-center justify-center text-gray-400 hover:text-[#B70F1D] transition-colors"
                            aria-label="Show current password">
                            <i class="fa-regular fa-eye text-sm sm:text-base"></i>
                        </button>

                    </div>
                    <small class="error-msg old_password_error block mt-1.5 text-xs text-red-600 font-medium hidden"></small>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-5 sm:gap-6 mt-6 sm:mt-7">
                    <div>
                        <label for="password" class="block text-xs sm:text-sm font-semibold text-gray-800 mb-2">New
                            Password
                            <span class="text-[#B70F1D]">*</span>
                        </label>

                        <div class="relative">
                            <input id="password" name="password" type="password"
                                placeholder="Enter your new password"
                                class="w-full h-10 sm:h-11 lg:h-12 rounded-lg border border-gray-200 bg-white px-3 sm:px-4 pr-11 sm:pr-12 text-xs sm:text-sm text-gray-800 placeholder:text-gray-400 outline-none transition-all duration-200 focus:border-[#B70F1D] focus:ring-2 focus:ring-[#B70F1D]/10" />
                            <button type="button" data-toggle-password="password"
                                class="js-toggle-password absolute right-0 top-0 h-full w-10 sm:w-12 flex items-center justify-center text-gray-400 hover:text-[#B70F1D] transition-colors"
                                aria-label="Show new password">
                                <i class="fa-regular fa-eye text-sm sm:text-base"></i>
                            </button>

                        </div>
                        <small class="error-msg password_error block mt-1.5 text-xs text-red-600 font-medium hidden"></small>

                    </div>
                    <div>
                        <label for="password_confirmation" class="block text-xs sm:text-sm font-semibold text-gray-800 mb-2">
                            Confirm New Password
                            <span class="text-[#B70F1D]">*</span>
                        </label>
                        <div class="relative">
                            <input id="password_confirmation" name="password_confirmation" type="password"
                                placeholder="Confirm your new password"
                                class="w-full h-10 sm:h-11 lg:h-12 rounded-lg border border-gray-200 bg-white px-3 sm:px-4 pr-11 sm:pr-12 text-xs sm:text-sm text-gray-800 placeholder:text-gray-400 outline-none transition-all duration-200 focus:border-[#B70F1D] focus:ring-2 focus:ring-[#B70F1D]/10" />
                            <button type="button" data-toggle-password="password_confirmation"
                                class="js-toggle-password absolute right-0 top-0 h-full w-10 sm:w-12 flex items-center justify-center text-gray-400 hover:text-[#B70F1D] transition-colors"
                                aria-label="Show confirm password">
                                <i class="fa-regular fa-eye text-sm sm:text-base"></i>
                            </button>
                        </div>
                        <small class="error-msg password_confirmation_error block mt-1.5 text-xs text-red-600 font-medium hidden"></small>

                    </div>
                </div>
            </div>

            <div
                class="border-t border-gray-100 px-4 sm:px-6 lg:px-8 py-4 sm:py-5 flex flex-col-reverse sm:flex-row sm:items-center sm:justify-end gap-3">
                <button type="button" onclick="window.history.back()"
                    class="w-full sm:w-auto inline-flex items-center justify-center h-10 sm:h-11 px-5 sm:px-6 rounded-lg border border-gray-200 bg-white text-gray-700 text-xs sm:text-sm font-medium hover:bg-gray-50 hover:border-gray-300 transition-all duration-200">
                    Cancel
                </button>
                <button type="submit" id="updatePasswordBtn"
                    class="w-full sm:w-auto inline-flex items-center justify-center gap-2 h-10 sm:h-11 px-5 sm:px-6 lg:px-7 rounded-lg bg-[#B70F1D] hover:bg-[#A20D19] active:bg-[#8F0B16] text-white text-xs sm:text-sm font-semibold shadow-sm hover:shadow-md transition-all duration-200">
                    <i class="fa-solid fa-lock text-xs sm:text-sm"></i>
                    <span>Update Password</span>
                </button>
            </div>
        </form>
    </div>

    @push('scripts')
        <script>
            // ---------------------------------------------------------------
            // SHOW / HIDE PASSWORD
            // Buttons carry data-toggle-password="<inputId>" and an icon inside.
            // ---------------------------------------------------------------
            document.querySelectorAll('.js-toggle-password').forEach(function (btn) {

                btn.addEventListener('click', function () {

                    const inputId = this.getAttribute('data-toggle-password');
                    const input = document.getElementById(inputId);
                    const icon = this.querySelector('i');

                    if (!input) return;

                    if (input.type === 'password') {
                        input.type = 'text';
                        icon.classList.remove('fa-eye');
                        icon.classList.add('fa-eye-slash');
                        this.setAttribute('aria-label', 'Hide password');
                    } else {
                        input.type = 'password';
                        icon.classList.remove('fa-eye-slash');
                        icon.classList.add('fa-eye');
                        this.setAttribute('aria-label', 'Show password');
                    }

                });

            });

            // ---------------------------------------------------------------
            // UNIFIED ALERT HANDLING
            // Shows exactly one alert: sessionStorage (from a just-completed
            // AJAX action) takes priority; otherwise falls back to a
            // server-rendered flash message. Never both at once.
            // ---------------------------------------------------------------
            function showGlobalAlert(type, message) {
                if (!message) return;

                const box = document.getElementById('globalAlert');
                const icon = document.getElementById('globalAlertIcon');
                const text = document.getElementById('globalAlertText');

                box.classList.remove('hidden');
                box.classList.add('flex');

                box.classList.remove(
                    'bg-green-50', 'border-green-200', 'text-green-700',
                    'bg-red-50', 'border-red-200', 'text-red-700'
                );
                icon.classList.remove('fa-circle-check', 'fa-circle-exclamation');

                if (type === 'success') {
                    box.classList.add('bg-green-50', 'border-green-200', 'text-green-700');
                    icon.classList.add('fa-circle-check');
                } else {
                    box.classList.add('bg-red-50', 'border-red-200', 'text-red-700');
                    icon.classList.add('fa-circle-exclamation');
                }

                text.textContent = message;

                setTimeout(() => {
                    box.scrollIntoView({ behavior: 'smooth', block: 'center' });
                }, 50);

                clearTimeout(window.__globalAlertTimer);
                window.__globalAlertTimer = setTimeout(() => {
                    box.classList.add('hidden');
                    box.classList.remove('flex');
                }, 5000);
            }

            document.getElementById('globalAlertClose').addEventListener('click', function () {
                const box = document.getElementById('globalAlert');
                box.classList.add('hidden');
                box.classList.remove('flex');
                clearTimeout(window.__globalAlertTimer);
            });

            window.addEventListener('DOMContentLoaded', () => {
                const successMsg = sessionStorage.getItem('successMessage');
                const errorMsg = sessionStorage.getItem('errorMessage');

                if (successMsg) {
                    showGlobalAlert('success', successMsg);
                    sessionStorage.removeItem('successMessage');
                } else if (errorMsg) {
                    showGlobalAlert('error', errorMsg);
                    sessionStorage.removeItem('errorMessage');
                } else {
                    const box = document.getElementById('globalAlert');
                    const serverSuccess = box.dataset.serverSuccess;
                    const serverError = box.dataset.serverError;

                    if (serverSuccess) {
                        showGlobalAlert('success', serverSuccess);
                    } else if (serverError) {
                        showGlobalAlert('error', serverError);
                    }
                }

                // Make sure no stale field errors are visible on a fresh load.
                document.querySelectorAll('.error-msg').forEach(el => {
                    el.textContent = '';
                    el.classList.add('hidden');
                });
            });

            function showFieldError(field, message) {
                const el = document.querySelector('.' + field + '_error');
                if (!el) return;
                el.textContent = message;
                el.classList.toggle('hidden', !message);
            }

            function clearFieldError(field) {
                showFieldError(field, '');
            }

            function clearAllFieldErrors() {
                document.querySelectorAll('.error-msg').forEach(el => {
                    el.textContent = '';
                    el.classList.add('hidden');
                });
            }

            {{-- AJAX Validation --}}
            $(document).ready(function () {

                // CURRENT PASSWORD
                function validateOldPassword() {
                    let password = $('#old_password').val().trim();

                    clearFieldError('old_password');

                    if (password === '') {
                        showFieldError('old_password', 'Current password field is required.');
                        return false;
                    }

                    return true;
                }

                // NEW PASSWORD
                function validatePassword() {
                    let password = $('#password').val().trim();
                    const pattern = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[!@#$%^&*]).{8,}$/;

                    clearFieldError('password');

                    if (password === '') {
                        showFieldError('password', 'New password field is required.');
                        return false;
                    }

                    if (!pattern.test(password)) {
                        showFieldError('password', 'Password must be at least 8 characters and include an uppercase letter, a lowercase letter, a number, and a special character.');
                        return false;
                    }

                    return true;
                }

                // CONFIRM PASSWORD
                function validateConfirmPassword() {
                    let password = $('#password').val();
                    let confirmPassword = $('#password_confirmation').val();

                    clearFieldError('password_confirmation');

                    if (confirmPassword === '') {
                        showFieldError('password_confirmation', 'Confirm password field is required.');
                        return false;
                    }

                    if (password !== confirmPassword) {
                        showFieldError('password_confirmation', 'Confirm Password must match the New Password.');
                        return false;
                    }

                    return true;
                }

                // EVENTS
                $('#old_password').on('blur', validateOldPassword);
                $('#old_password').on('input', function () { clearFieldError('old_password'); });

                $('#password').on('blur', function () {
                    validatePassword();
                    validateConfirmPassword();
                });
                $('#password').on('input', function () { clearFieldError('password'); });

                $('#password_confirmation').on('blur', validateConfirmPassword);
                $('#password_confirmation').on('input', function () { clearFieldError('password_confirmation'); });

                $('#passwordForm').on('submit', async function (e) {

                    e.preventDefault();

                    clearAllFieldErrors();

                    const validOldPassword = validateOldPassword();
                    const validPassword = validatePassword();
                    const validConfirmPassword = validateConfirmPassword();

                    if (!validOldPassword || !validPassword || !validConfirmPassword) {
                        return;
                    }

                    let formData = new FormData();

                    formData.append('_token', $('meta[name="csrf-token"]').attr('content'));
                    formData.append('old_password', $('#old_password').val());
                    formData.append('password', $('#password').val());
                    formData.append('password_confirmation', $('#password_confirmation').val());

                    $.ajax({

                        url: "{{ route('admin.password.update') }}",
                        type: "POST",
                        data: formData,
                        processData: false,
                        contentType: false,

                        beforeSend: function () {

                            $('#updatePasswordBtn')
                                .prop('disabled', true)
                                .html('<i class="fa-solid fa-spinner fa-spin text-xs sm:text-sm"></i><span>Processing...</span>');
                        },

                        success: function (response) {

                            $('#updatePasswordBtn')
                                .prop('disabled', false)
                                .html('<i class="fa-solid fa-lock text-xs sm:text-sm"></i><span>Update Password</span>');

                            sessionStorage.setItem("successMessage", response.message);
                            window.location.href = response.redirect;

                        },

                        error: function (xhr) {

                            $('#updatePasswordBtn')
                                .prop('disabled', false)
                                .html('<i class="fa-solid fa-lock text-xs sm:text-sm"></i><span>Update Password</span>');

                            clearAllFieldErrors();

                            if (xhr.status == 422) {

                                if (xhr.responseJSON.errors) {

                                    $.each(xhr.responseJSON.errors, function (key, value) {
                                        showFieldError(key, value[0]);
                                    });

                                }

                                if (xhr.responseJSON.message) {
                                    showFieldError('old_password', xhr.responseJSON.message);
                                }

                            } else {

                                showGlobalAlert('error', (xhr.responseJSON && xhr.responseJSON.message) || 'Something went wrong. Please try again.');

                            }

                        }

                    });

                });
            });
        </script>
    @endpush

@endsection