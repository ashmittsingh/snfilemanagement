<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Shiv Naresh - Admin Forgot Password</title>

  <link rel="stylesheet"
    href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.3.1/css/all.min.css"
    integrity="sha512-QeR2VH+lsBE5LSAe1Q5EnTBbe7XTBubt8dG93Y7gidSgdMCr8nVqKcfKAMyN96SV8KDbZVTDXChatu5G2KQGzg=="
    crossorigin="anonymous" referrerpolicy="no-referrer" />
  <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
  <link rel="stylesheet"
    href="{{ asset('admin_assets/assets/css/main.css') }}?v={{ time() }}">
</head>

<body class="antialiased bg-gray-100">
  <div
    class="min-h-screen w-full xl:flex xl:items-center xl:justify-center bg-gray-100">
    <div
      class="relative min-h-screen 2xl:min-h-0 w-full 2xl:h-[100vh] 2xl:max-h-[860px] 2xl:max-w-[1600px] bg-[#B70F1D] flex overflow-hidden rounded-none 2xl:shadow-2xl">
      <div
        class="hidden lg:flex relative w-1/2 shrink-0 bg-white flex-col overflow-hidden">
        <div
          class="absolute -top-24 -right-20 w-72 h-72 rounded-full border border-red-200/60">
        </div>
        <div
          class="absolute -top-16 -right-4 w-56 h-56 rounded-full border border-red-200/40">
        </div>
        <div
          class="absolute top-[15%] right-[7%] w-3 h-3 bg-red-300/70 rounded-full">
        </div>
        <div
          class="absolute top-[30%] right-[22%] w-2.5 h-2.5 bg-red-300/70 rounded-full">
        </div>
        <div
          class="absolute top-[32%] left-[48%] w-2 h-2 bg-red-300/60 rounded-full">
        </div>
        <div
          class="absolute top-[16%] -right-10 w-44 h-44 rounded-full bg-red-50/80">
        </div>
        <div
          class="absolute -bottom-20 left-0 w-72 h-72 rounded-full bg-red-50/70">
        </div>
        <div
          class="absolute -bottom-12 -left-8 w-80 h-80 rounded-full border border-red-200/50">
        </div>
        <div class="relative z-20 px-8 xl:px-12 pt-6 xl:pt-8">
          <a href="{{ route('frontend.index') }}"><img
              src="{{ asset('admin_assets/assets/images/logo.png') }}"
              alt="Shiv Naresh"
              class="w-[145px] xl:w-[165px] h-auto object-contain"></a>
          <p class="text-[12px] xl:text-[13px] text-gray-500 font-medium mt-2">
            Your Trusted Shopping Partner</p>
        </div>
        <div class="relative z-20 flex-1">
          <div
            class="absolute left-10 lg:top-[20%] xl:left-14 top-[10%] max-w-[330px]">
            <h1
              class="text-[32px] xl:text-[36px] 2xl:text-[40px] leading-[1.18] lg:font-extrabold tracking-tight text-gray-800">
              Shop
              <span class="text-red-600">Smarter.</span><br>Live
              <span class="text-red-600">Better.</span>
            </h1>
            <div class="mt-6 w-12 h-[3px] bg-red-600"></div>
            <p
              class="mt-6 text-[17px] xl:text-[18px] leading-8 text-gray-600 max-w-[290px]">
              Discover quality
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
            class="absolute z-10 bottom-[-38px] right-[60px] lg:right-[10px] w-[58%] lg:w-[95%] xl:w-[60%] 2xl:w-[70%]">
            <img src="{{ asset('admin_assets/assets/images/login-img.png') }}"
              alt="Shiv Naresh Shopping"
              class="w-full h-auto object-contain object-bottom">
          </div>
        </div>
        <div class="relative z-30 px-10 xl:px-14 pb-5">
          <p class="text-[12px] xl:text-[13px] text-gray-400">© {{ date('Y') }}
            Shiv Naresh</p>
        </div>
      </div>
      <div
        class="relative flex-1 min-w-0 min-h-screen 2xl:min-h-0 flex flex-col justify-center items-center px-5 py-8 sm:px-8 sm:py-10 md:px-12 lg:px-14 xl:px-20 2xl:px-28 bg-white lg:bg-[#B70F1D] ">
        <div class="lg:hidden z-20 mb-18 ">
          <a href="{{ route('frontend.index') }}"><img
              src="{{ asset('admin_assets/assets/images/logo.png') }}"
              alt="Shiv Naresh"
              class="w-[145px] xl:w-[165px] h-auto object-contain"></a>
          <p class="text-[12px] xl:text-[13px] text-gray-500 font-medium mt-2">
            Your Trusted Shopping Partner</p>
        </div>
        <div
          class="w-full max-w-[380px] sm:max-w-[420px] lg:max-w-[460px] xl:max-w-[500px] bg-[#B70F1D] px-8 py-10 rounded-2xl">
          <h1
            class="text-white font-extrabold text-3xl sm:text-4xl md:text-5xl lg:text-5xl mb-6 sm:mb-8 lg:mb-9">
            Forgot Password</h1>
          <h3 class="text-white mb-6">Enter your email address and we'll <br>
            send you a link to reset your
            password.</h3>

          <form id="formAuthentication" class="space-y-5 sm:space-y-6"
            action="{{ route('admin.forgot.password.submit') }}" method="POST">
            <div>
              @csrf
              @if (session('error'))
              <div
                class="flex items-center gap-2 bg-white text-red-700 text-sm sm:text-base rounded-xl px-4 py-3">
                <i class="fa-solid fa-circle-exclamation"></i>
                {{ session('error') }}
              </div>
              @endif
              @if (session('success'))
              <div
                class="flex items-center gap-2 bg-white text-green-700 text-sm sm:text-base rounded-xl px-4 py-3">
                <i class="fa-solid fa-circle-check"></i>
                {{ session('success') }}
              </div>
              @endif
              <div id="forgotMessage" class="mb-1"></div>
              <label for="email"
                class="block text-white font-semibold text-sm sm:text-base mb-2">Email
                Address <span class="text-danger">*</span></label>

              <div class="relative">
                <i
                  class="fa-regular fa-envelope absolute left-4 top-1/2 -translate-y-1/2 text-sm sm:text-base pointer-events-none
                                {{ $errors->has('email') ? 'text-danger' : 'text-white/50' }}"></i>

                <input id="email" name="email" type="text" autocomplete="email"
                  placeholder="Enter your email"
                  class="w-full rounded-3xl bg-black/25 text-white placeholder:text-white/50 text-sm sm:text-base pl-10 sm:pl-11 pr-4 py-3.5 sm:py-4 outline-none focus:ring-2 transition
                                    {{ $errors->has('email') ? 'ring-2 ring-danger focus:ring-danger' : 'focus:ring-white/50' }}" />
              </div>

              @error('email')
              <span
                class="error-msg flex items-center gap-1.5 mt-2 text-white text-xs sm:text-sm">
                <i class="fa-solid fa-circle-exclamation text-danger"></i>
                {{ $message }}
              </span>
              @enderror
              <span
                class="error-msg flex items-center gap-1.5 mt-2 text-white text-xs sm:text-sm"
                id="email_error"></span>
            </div>
            <button type="submit" id="submitBtn"
              class="w-full flex items-center justify-center bg-white text-red-700 font-bold text-sm sm:text-base rounded-full py-3.5 sm:py-4 mt-1 hover:bg-red-50 active:scale-[0.99] shadow-lg transition">
              <span id="btnText" class="flex items-center gap-2">
                <i class="fa-solid fa-paper-plane"></i>
                Send Reset Link
              </span>
              <span id="btnLoader" class="hidden items-center gap-2">
                <i class="fa-solid fa-spinner fa-spin"></i>
                Sending...
              </span>
            </button>
            <div class="flex justify-center text-white">
              <p><i class="fa-solid fa-arrow-left"></i>&nbsp;<a
                  href="{{ route('admin.login.view') }}">Back to
                  Login</a></p>
            </div>
          </form>
        </div>
        <div
          class="lg:hidden relative z-30 px-10 xl:px-14 pt-10 w-full h-20% flex items-center justify-center">
          <p class="text-[12px] xl:text-[13px] text-gray-400">© {{ date('Y') }}
            Shiv Naresh</p>
        </div>
      </div>
    </div>
</body>
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

<script>
  function validateEmail() {

    let email = $('#email').val().trim();
    let error = '';

    if (email == '') {

      error = 'Email field is required.';

    } else {

      let pattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

      if (!pattern.test(email)) {
        error = 'Please enter a valid email address.';
      }
    }

    $('#email_error').html(error);

    return error == '';
  }

  $('#email').on('blur keyup', function() {
    validateEmail();
  });

  $('#formAuthentication').submit(function(e) {

    e.preventDefault();

    $('#forgotMessage').html('');

    if (!validateEmail()) {
      return;
    }

    $.ajax({
      beforeSend: function() {

        $('#submitBtn').prop('disabled', true);
        $('#btnText').addClass('hidden').removeClass('flex');
        $('#btnLoader').removeClass('hidden').addClass('flex');

      },

      url: "{{ route('admin.forgot.password.submit') }}",
      type: "POST",
      data: $(this).serialize(),

      success: function(response) {
        $('#submitBtn').prop('disabled', false);
        $('#btnLoader').addClass('hidden').removeClass('flex');
        $('#btnText').removeClass('hidden').addClass('flex');

        $('#forgotMessage').html(
          '<div class="flex items-center gap-2 bg-white text-green-700 text-sm sm:text-base rounded-xl px-4 py-3">' +
          '<i class="fa-solid fa-circle-check"></i>' +
          response.message +
          '</div>'
        );

        $('#formAuthentication')[0].reset();
        $('#email_error').html('');

      },

      error: function(xhr) {

        $('#submitBtn').prop('disabled', false);
        $('#btnLoader').addClass('hidden').removeClass('flex');
        $('#btnText').removeClass('hidden').addClass('flex');
        if (xhr.status == 422) {

          let errors = xhr.responseJSON.errors;

          $('#email_error').html(errors.email ? errors.email[0] : '');

        } else {

          $('#forgotMessage').html(
            '<div class="flex items-center gap-2 bg-white text-red-700 text-sm sm:text-base rounded-xl px-4 py-3">' +
            '<i class="fa-solid fa-circle-exclamation"></i>' +
            xhr.responseJSON.message +
            '</div>'
          );

        }

      }

    });

  });
</script>

</html>