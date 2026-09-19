@extends('layout.admin.app')

@section('content')

    


    <div class="flex justify-between mb-6 sm:my-8 bg-white p-4">
        <div>
            <h1 class="text-xl sm:text-2xl font-bold text-[#B70F1D] leading-none">Edit Password</h1>
            <p class="text-xs sm:text-sm text-gray-400 mt-1">Edit your password to keep your account
                secure</p>
        </div>
    </div>

    @if (Session::has('success'))
        <div class="alert alert-success">{{ Session::get('success') }}</div>
    @endif

    @if (Session::has('error'))
        <div class="alert alert-danger">{{ Session::get('error') }}</div>
    @endif

    <div id="formMessage" class="alert d-none"></div>
    <div
        class="mx-4 sm:mx-6 lg:mx-8 bg-white border border-gray-100 rounded-2xl shadow-[0_2px_20px_rgba(0,0,0,0.05)] overflow-hidden">
        <form id="passwordForm" class="flex flex-col">
            
            <div class="px-4 sm:px-6 lg:px-8 py-6 sm:py-7 lg:py-8">
                <div>
                    <label for="currentPassword" class="block text-xs sm:text-sm font-semibold text-gray-800 mb-2">
                        Current Password
                        <span class="text-[#B70F1D]">*</span>
                    </label>
                    <div class="relative">
                        <input id="old_password" name="old_password" type="password" 
                            placeholder="Enter your current password" 
                            class="w-full h-10 sm:h-11 lg:h-12 rounded-lg border border-gray-200 bg-white px-3 sm:px-4 pr-11 sm:pr-12 text-xs sm:text-sm text-gray-800 placeholder:text-gray-400 outline-none transition-all duration-200 focus:border-[#B70F1D] focus:ring-2 focus:ring-[#B70F1D]/10" />
                        
                             <button type="button" data-toggle-password="currentPassword"
                            class="absolute right-0 top-0 h-full w-10 sm:w-12 flex items-center justify-center text-gray-400 hover:text-[#B70F1D] transition-colors"
                            aria-label="Show current password">
                            <i class="fa-regular fa-eye text-sm sm:text-base"></i>
                        </button>

                    </div>
                    <small class="error-msg old_password_error"></small>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-5 sm:gap-6 mt-6 sm:mt-7">
                    <div>
                        <label for="newPassword" class="block text-xs sm:text-sm font-semibold text-gray-800 mb-2">New
                            Password
                            <span class="text-[#B70F1D]">*</span>
                        </label>

                        <div class="relative">
                            <input id="password" name="password" type="password" 
                                placeholder="Enter your new password" 
                                class="w-full h-10 sm:h-11 lg:h-12 rounded-lg border border-gray-200 bg-white px-3 sm:px-4 pr-11 sm:pr-12 text-xs sm:text-sm text-gray-800 placeholder:text-gray-400 outline-none transition-all duration-200 focus:border-[#B70F1D] focus:ring-2 focus:ring-[#B70F1D]/10" />
                            <button type="button" data-toggle-password="newPassword"
                                class="absolute right-0 top-0 h-full w-10 sm:w-12 flex items-center justify-center text-gray-400 hover:text-[#B70F1D] transition-colors"
                                aria-label="Show new password">
                                <i class="fa-regular fa-eye text-sm sm:text-base"></i>
                            </button>
                            
                        </div>
                        <small class="error-msg password_error"></small>
                        
                    </div>
                    <div>
                        <label for="confirmPassword" class="block text-xs sm:text-sm font-semibold text-gray-800 mb-2">
                            Confirm New Password
                            <span class="text-[#B70F1D]">*</span>
                        </label>
                        <div class="relative">
                            <input id="password_confirmation" name="password_confirmation" type="password" 
                                placeholder="Confirm your new password" 
                                class="w-full h-10 sm:h-11 lg:h-12 rounded-lg border border-gray-200 bg-white px-3 sm:px-4 pr-11 sm:pr-12 text-xs sm:text-sm text-gray-800 placeholder:text-gray-400 outline-none transition-all duration-200 focus:border-[#B70F1D] focus:ring-2 focus:ring-[#B70F1D]/10" />
                            <button type="button" data-toggle-password="confirmPassword"
                                class="absolute right-0 top-0 h-full w-10 sm:w-12 flex items-center justify-center text-gray-400 hover:text-[#B70F1D] transition-colors"
                                aria-label="Show confirm password">
                                <i class="fa-regular fa-eye text-sm sm:text-base"></i>
                            </button>
                        </div>
                         <small class="error-msg password_confirmation_error"></small>
                        
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

        <script>
        document.querySelectorAll('.toggle-password').forEach(function (icon) {

            icon.addEventListener('click', function () {

                let input = this.previousElementSibling;

                if (input.type === "password") {

                    input.type = "text";

                    this.classList.remove('bx-hide');
                    this.classList.add('bx-show');

                } else {

                    input.type = "password";

                    this.classList.remove('bx-show');
                    this.classList.add('bx-hide');

                }

            });

        });
    </script>

    @push('scripts')
        {{-- AJAX Validation --}}
        <script>
            $(document).ready(function () {
                function showError(field, message) {
                    $('.' + field + '_error').text(message);
                }

                function clearError(field) {
                    $('.' + field + '_error').text('');
                }

                // CURRENT PASSWORD
                function validateOldPassword() {
                    let password = $('#old_password').val().trim();

                    clearError('old_password');

                    if (password === '') {
                        showError('old_password', 'Current password field is required.');
                        return false;
                    }

                    return true;
                }

                // NEW PASSWORD
                function validatePassword() {
                    let password = $('#password').val().trim();
                    const pattern = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[!@#$%^&*]).{8,}$/;

                    clearError('password');

                    if (password === '') {
                        showError('password', 'New password field is required.');
                        return false;
                    }

                    if (password.length < 8) {
                        showError('password', 'Password must be at least 8 characters long and contain at least one uppercase letter, one lowercase letter, one number, and one special character.');
                        return false;
                    }

                    if (!pattern.test(password)) {
                        showError('password', 'Password must be at least 8 characters long and contain at least one uppercase letter, one lowercase letter, one number, and one special character.');
                        return false;
                    }

                    return true;
                }

                // CONFIRM PASSWORD
                function validateConfirmPassword() {
                    let password = $('#password').val();
                    let confirmPassword = $('#password_confirmation').val();

                    clearError('password_confirmation');

                    if (confirmPassword === '') {
                        showError('password_confirmation', 'Confirm password field is required.');
                        return false;
                    }

                    if (password !== confirmPassword) {
                        showError('password_confirmation', 'Confirm Password must match the New Password.');
                        return false;
                    }

                    return true;
                }

                // EVENTS
                $('#old_password').on('blur', validateOldPassword);
                $('#password').on('blur', function () {
                    validatePassword();
                    validateConfirmPassword();
                });

                $('#password_confirmation').on('blur', validateConfirmPassword);

                $('#passwordForm').on('submit', async function (e) {

                    e.preventDefault();

                    $('.error-msg').text('');

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
                                .html('Processing...');
                        },

                        success: function (response) {

                            $('#updatePasswordBtn')
                                .prop('disabled', false)
                                .html('Update Password <i class="bx bx-check"></i>');

                            sessionStorage.setItem("successMessage", response.message);
                            window.location.href = response.redirect;

                        },

                        error: function (xhr) {

                            $('#updatePasswordBtn')
                                .prop('disabled', false)
                                .html('Update Password <i class="bx bx-check"></i>');

                            $('.error-msg').text('');

                            if (xhr.status == 422) {

                                if (xhr.responseJSON.errors) {

                                    $.each(xhr.responseJSON.errors, function (key, value) {
                                        $('.' + key + '_error').text(value[0]);
                                    });

                                }

                                if (xhr.responseJSON.message) {
                                    $('.old_password_error').text(xhr.responseJSON.message);
                                }

                            } else {

                                alert(xhr.responseJSON.message);

                            }

                        }

                    });

                });
            });
        </script>
    @endpush

@endsection