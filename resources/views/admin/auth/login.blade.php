<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Shiv Naresh - Admin Login</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.3.1/css/all.min.css"
        integrity="sha512-QeR2VH+lsBE5LSAe1Q5EnTBbe7XTBubt8dG93Y7gidSgdMCr8nVqKcfKAMyN96SV8KDbZVTDXChatu5G2KQGzg=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    <link rel="stylesheet" href="{{ asset('admin_assets/assets/css/main.css') }}?v={{ time() }}">
    <style>
        input:-webkit-autofill,
        input:-webkit-autofill:hover,
        input:-webkit-autofill:focus {
            -webkit-text-fill-color: #ffffff;
            caret-color: #ffffff;
            transition: background-color 9999s ease-in-out 0s;
            box-shadow: 0 0 0px 1000px rgba(0, 0, 0, 0.25) inset;
        }
    </style>
</head>

<body class="antialiased bg-gray-100">
    <div class="min-h-screen w-full 2xl:flex 2xl:items-center 2xl:justify-center bg-gray-100">
        <div class="relative min-h-screen 2xl:min-h-0 w-full 2xl:h-[100vh] 2xl:max-h-[860px] 2xl:max-w-[1600px] bg-[#B70F1D] flex overflow-hidden rounded-none 2xl:shadow-2xl">
            <div class="hidden lg:flex relative w-1/2 shrink-0 bg-white flex-col overflow-hidden">
            <div class="absolute -top-24 -right-20 w-72 h-72 rounded-full border border-red-200/60"></div>
            <div class="absolute -top-16 -right-4 w-56 h-56 rounded-full border border-red-200/40"></div>
            <div class="absolute top-[15%] right-[7%] w-3 h-3 bg-red-300/70 rounded-full"></div>
            <div class="absolute top-[30%] right-[22%] w-2.5 h-2.5 bg-red-300/70 rounded-full"></div>
            <div class="absolute top-[32%] left-[48%] w-2 h-2 bg-red-300/60 rounded-full"></div>
            <div class="absolute top-[16%] -right-10 w-44 h-44 rounded-full bg-red-50/80"></div>
            <div class="absolute -bottom-20 left-0 w-72 h-72 rounded-full bg-red-50/70"></div>
            <div class="absolute -bottom-12 -left-8 w-80 h-80 rounded-full border border-red-200/50"></div>
            <div class="relative z-20 px-8 xl:px-12 pt-6 xl:pt-8">
                <a href="{{ route('frontend.index') }}"><img src="{{ asset('admin_assets/assets/images/logo.png') }}" alt="Shiv Naresh"
                    class="w-[145px] xl:w-[165px] h-auto object-contain"></a>
                <p class="text-[12px] xl:text-[13px] text-gray-500 font-medium mt-2">Your Trusted Shopping Partner</p>
            </div>
            <div class="relative z-20 flex-1">
                <div class="absolute left-10 lg:top-[20%] xl:left-14 top-[10%] max-w-[330px]">
                    <h1
                        class="text-[32px] xl:text-[36px] 2xl:text-[40px] leading-[1.18] lg:font-extrabold tracking-tight text-gray-800">
                        Shop
                        <span class="text-red-600">Smarter.</span><br>Live
                        <span class="text-red-600">Better.</span>
                    </h1>
                    <div class="mt-6 w-12 h-[3px] bg-red-600"></div>
                    <p class="mt-6 text-[17px] xl:text-[18px] leading-8 text-gray-600 max-w-[290px]">Discover quality
                        products <br> that elevate your <br> everyday life.</p>
                </div>
                <div class="absolute top-[7%] left-[58%] text-red-300/60">
                    <i class="fa-solid fa-bag-shopping font-bold text-6xl"></i>
                </div>
                <div class="absolute top-[43%] left-[40%] text-red-300/60">
                    <i class="fa-solid fa-tag font-bold text-6xl"></i>
                </div>
                <div class="absolute bottom-[27%] left-[10%] text-red-300/60">
                    <i class="fa-solid fa-cart-shopping font-bold text-4xl"></i>
                </div>
                <div class="absolute bottom-[16%] right-[6%] text-red-300/60">
                    <i class="fa-solid fa-gift"></i>
                </div>
                <div
                    class="absolute z-10 bottom-[-38px] right-[60px] lg:right-[10px] w-[58%] lg:w-[95%] xl:w-[86%] 2xl:w-[70%]">
                    <img src="{{ asset('admin_assets/assets/images/login-img.png') }}" alt="Shiv Naresh Shopping"
                        class="w-full h-auto object-contain object-bottom">
                </div>
            </div>
            <div class="relative z-30 px-10 xl:px-14 pb-5">
                <p class="text-[12px] xl:text-[13px] text-gray-400">© {{ date('Y') }} Shiv Naresh</p>
            </div>
        </div>
        <div class="relative flex-1 min-w-0 min-h-screen 2xl:min-h-0 flex flex-col justify-center items-center px-5 py-8 sm:px-8 sm:py-10 md:px-12 lg:px-14 xl:px-20 2xl:px-28 bg-white lg:bg-[#B70F1D] ">
            <div class="lg:hidden z-20 mb-18 ">
                <a href="{{ route('frontend.index') }}"><img src="{{ asset('admin_assets/assets/images/logo.png') }}" alt="Shiv Naresh" class="w-[145px] xl:w-[165px] h-auto object-contain"></a>
                <p class="text-[12px] xl:text-[13px] text-gray-500 font-medium mt-2">Your Trusted Shopping Partner</p>
            </div>
            <div class="w-full max-w-[380px] sm:max-w-[420px] lg:max-w-[440px] bg-[#B70F1D] px-8 py-10 lg:px-2 lg:py-2
                rounded-4xl">
                <h1 class="text-white font-extrabold text-3xl sm:text-4xl md:text-5xl lg:text-5xl mb-4 sm:mb-6 lg:mb-7">
                    Login</h1>

                <p class="mb-4 text-white font-light">Login to continue to your dashboard</p>

                <form id="formAuthentication" class="space-y-5 sm:space-y-6" action="{{ route('admin.login.submit') }}"
                    method="POST">

                    @csrf
                    @if (session('error'))
    <div class="flex items-center gap-2 bg-white text-red-700 text-sm sm:text-base rounded-xl px-4 py-3">
        <i class="fa-solid fa-circle-exclamation"></i>
        {{ session('error') }}
    </div>
@endif

@if (session('success'))
    <div class="flex items-center gap-2 bg-white text-green-700 text-sm sm:text-base rounded-xl px-4 py-3">
        <i class="fa-solid fa-circle-check"></i>
        {{ session('success') }}
    </div>
@endif
<div id="loginMessage" class="mb-1"></div>

                    <div>
    <label for="email" class="block text-white font-semibold text-sm sm:text-base mb-2">Email
        Address <span class="text-danger">*</span></label>

    <div class="relative">
        <i class="fa-regular fa-envelope absolute left-4 top-1/2 -translate-y-1/2 text-sm sm:text-base pointer-events-none
            {{ $errors->has('email') ? 'text-danger' : 'text-white/50' }}"></i>

        <input id="email" name="email" type="text" placeholder="Enter your email address"
            class="w-full rounded-3xl bg-black/25 text-white placeholder:text-white/50 text-sm sm:text-base pl-10 sm:pl-11 pr-4 py-3.5 sm:py-4 outline-none focus:ring-2 transition
                {{ $errors->has('email') ? 'ring-2 ring-danger focus:ring-danger' : 'focus:ring-white/50' }}" />
    </div>

    @error('email')
        <span class="error-msg flex items-center gap-1.5 mt-2 text-white text-xs sm:text-sm">
            <i class="fa-solid fa-circle-exclamation text-danger"></i>
            {{ $message }}
        </span>
    @enderror
    <span class="error-msg flex items-center gap-1.5 mt-2 text-white text-xs sm:text-sm" id="email_error"></span>
</div>

                    <div>
    <label for="password"
        class="block text-white font-semibold text-sm sm:text-base mb-2">Password <span class="text-danger">*</span></label>

    <div class="relative">
        <i class="fa-solid fa-lock absolute left-4 top-1/2 -translate-y-1/2 text-sm sm:text-base pointer-events-none
            {{ $errors->has('password') ? 'text-danger' : 'text-white/50' }}"></i>

        <input id="password" name="password" type="password" placeholder="Enter your password"
            class="w-full rounded-3xl bg-black/25 text-white placeholder:text-white/50 text-sm sm:text-base pl-10 sm:pl-11 pr-11 sm:pr-12 py-3.5 sm:py-4 outline-none focus:ring-2 transition
                {{ $errors->has('password') ? 'ring-2 ring-danger focus:ring-danger' : 'focus:ring-white/50' }}" />

        <button type="button" class="toggle-password absolute right-4 top-1/2 -translate-y-1/2 text-white/50 hover:text-white transition"
            aria-label="Toggle password visibility" onclick="togglePassword()">
            <i id="eyeIcon" class="fa-regular fa-eye text-sm sm:text-base"></i>
        </button>
    </div>

    @error('password')
        <span class="error-msg flex items-center gap-1.5 mt-2 text-white text-xs sm:text-sm">
            <i class="fa-solid fa-circle-exclamation text-danger"></i>
            {{ $message }}
        </span>
    @enderror
    <span class="error-msg flex items-center gap-1.5 mt-2 text-white text-xs sm:text-sm" id="password_error"></span>
    <div class="text-right mt-2">
        <a href="{{ route('admin.forgot.password.view') }}"
            class="text-xs sm:text-sm text-red-100/80 hover:text-white underline underline-offset-2 transition">Forgot
            Password?</a>
    </div>
</div>
                    <button type="submit" id="loginBtn"
    class="w-full flex items-center justify-center gap-2 bg-white text-red-700 font-bold text-sm sm:text-base rounded-full py-3.5 sm:py-4 mt-1 hover:bg-red-50 active:scale-[0.99] shadow-lg transition">
    <span>Login</span>
</button>
                    <div></div>
                    <p class="text-sm text-white text-center font-light"><i class="fa-solid fa-lock mr-2"></i>Your
                        information is securely protected</p>
                </form>
            </div>
            <div class="lg:hidden relative z-30 px-10 xl:px-14 pt-10 w-full h-20% flex items-center justify-center">
                <p class="text-[12px] xl:text-[13px] text-gray-400">© {{ date('Y') }} Shiv Naresh</p>
            </div>
        </div>
    </div>
</div>
</body>
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script>
    function togglePassword() {
    const passwordInput = document.getElementById('password');
    const eyeIcon = document.getElementById('eyeIcon');

    if (passwordInput.type === 'password') {
        passwordInput.type = 'text';
        eyeIcon.classList.remove('fa-regular', 'fa-eye');
        eyeIcon.classList.add('fa-solid', 'fa-eye-slash');
    } else {
        passwordInput.type = 'password';
        eyeIcon.classList.remove('fa-solid', 'fa-eye-slash');
        eyeIcon.classList.add('fa-regular', 'fa-eye');
    }
}
    function validateField(field) {

        let value = $('#' + field).val().trim();
        let error = '';

        if (field == 'email') {

            if (value == '') {
                error = 'Email field is required.';
            } else {

                let emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

                if (!emailPattern.test(value)) {
                    error = 'Please enter a valid email address.';
                }
            }

        }

        if (field == 'password') {

            if (value == '') {
                error = 'Password field is required.';
            }

        }

        $('#' + field + '_error').text(error);

            return error == '';
        }

        // On Blur
        $('#email,#password').on('blur', function () {
            validateField($(this).attr('id'));
        });

        // Submit
        $('#formAuthentication').submit(function (e) {

        e.preventDefault();

        $('#loginMessage').html('');

        let emailValid = validateField('email');
        let passwordValid = validateField('password');

        if (!emailValid || !passwordValid) {
            return;
        }

        $('#loginBtn').prop('disabled', true).html(`
            <i class="fa-solid fa-spinner fa-spin"></i>
            <span>Logging in...</span>
        `);

        $.ajax({

            url: "{{ route('admin.login.submit') }}",
            type: "POST",
            data: $(this).serialize(),

            success: function (response) {

                if (response.status) {
                    window.location.href = response.redirect;
                }

            },

            error: function (xhr) {

                if (xhr.status == 422) {

                    let errors = xhr.responseJSON.errors;

                    $('.error-msg').html('');

                    $.each(errors, function (key, value) {
                        $('#' + key + '_error').html(value[0]);
                    });

                } else {

                    $('#loginMessage').html(
                        '<div class="flex items-center gap-2 bg-white text-red-700 text-sm sm:text-base rounded-xl px-4 py-3">' +
                        '<i class="fa-solid fa-circle-exclamation"></i>' +
                        xhr.responseJSON.message +
                        '</div>'
                    );
                }

                $('#loginBtn').prop('disabled', false).html(`<span>Login</span>`);
            }

        });

    });
</script>

</html>