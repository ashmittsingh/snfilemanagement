<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Shiv Naresh - {{ $pageTitle ?? '' }}</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.3.1/css/all.min.css"
        integrity="sha512-QeR2VH+lsBE5LSAe1Q5EnTBbe7XTBubt8dG93Y7gidSgdMCr8nVqKcfKAMyN96SV8KDbZVTDXChatu5G2KQGzg=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    <link rel="stylesheet" href="{{ asset('admin_assets/assets/css/index.css') }}?v={{ time() }}">
</head>

<body class="bg-white text-gray-800 antialiased">
    <div class="min-h-screen w-full 2xl:flex 2xl:items-center 2xl:justify-center bg-gray-100">
        <div class="relative min-h-screen 2xl:min-h-0 w-full 2xl:h-[100vh] 2xl:max-h-[860px] 2xl:max-w-[1600px] bg-gray-200 flex overflow-hidden rounded-none 2xl:shadow-2xl">
            <div class="flex w-full min-h-screen 2xl:min-h-0 2xl:h-full">
                <div id="sidebarOverlay" class="fixed inset-0 bg-black/40 z-40 hidden md:hidden"></div>
                <aside id="sidebar"
                    class="bg-gradient-to-b from-[#C40F1E] to-[#B70F1D] h-screen 2xl:h-full w-64 fixed 2xl:static top-0 left-0 z-50 flex flex-col px-3 lg:px-4 py-5 lg:py-6 shrink-0 -translate-x-full md:translate-x-0 2xl:translate-x-0 transition-all duration-300 ease-in-out shadow-xl 2xl:shadow-none">
                    <div class="relative">
                        <div id="sidebarBrand" class="relative flex flex-col items-center justify-center mb-6">
                            <div class="relative w-full flex items-center justify-center">
                                <div
                                    class="w-24 h-16 lg:w-28 lg:h-[68px] bg-white rounded-xl flex items-center justify-center shadow-sm">
                                   <a href="{{ route('admin.dashboard') }}"> <img src="{{ asset('admin_assets/assets/images/logo.png') }}"
                                        alt="Shiv Naresh Logo" class="w-20 lg:w-24 h-auto object-contain"></a>
                                </div>
                                <button id="toggleSidebar" type="button"
                                    class="hidden md:flex absolute -right-2 top-1/2 -translate-y-1/2 translate-x-full w-8 h-8 items-center justify-center rounded-lg bg-white/10 text-white hover:bg-white/20 transition-all"
                                    aria-label="Toggle sidebar">
                                    <i class="fa-solid fa-bars text-sm"></i>
                                </button>
                                <button id="closeSidebar" type="button"
                                    class="md:hidden absolute right-1 top-1 w-8 h-8 flex items-center justify-center rounded-lg text-white hover:bg-white/10 transition-colors"
                                    aria-label="Close sidebar">
                                    <i class="fa-solid fa-xmark"></i>
                                </button>
                            </div>
                            <div id="adminLabel" class="mt-3 text-center transition-all duration-300">
                                <p class="text-white font-semibold text-xs tracking-[0.22em]">ADMIN PANEL</p>
                                <p class="text-white/50 text-[9px] mt-1">Management Portal</p>
                            </div>
                        </div>
                        <div class="border-t border-white/15 mb-5"></div>

                        <nav class="space-y-1">
                            <p id="mainMenuLabel"
                                class="px-3 mb-2 text-[10px] font-semibold tracking-[0.16em] text-white/45 uppercase">Main Menu
                            </p>

                            <a href="{{ route('admin.dashboard') }}"
                                class="sidebar-link flex items-center gap-3 px-3.5 py-3 rounded-xl text-white font-semibold transition-all duration-200">
                                <span class="sidebar-icon w-6 h-6 shrink-0 flex items-center justify-center">
                                    <i class="fa-solid fa-folder text-[15px]"></i>
                                </span>
                                <span class="sidebar-text text-sm flex-1">Files</span>
                            </a>
                        </nav>
                    </div>
                    <div class="relative mt-auto">
                        <div class="border-t border-white/15 mb-4"></div>
                        <p id="systemLabel"
                            class="sidebar-text px-3 mb-2 text-[10px] font-semibold tracking-[0.16em] text-white/45 uppercase">
                            System</p>

                        <a href="{{ route('admin.logout') }}"
                            class="sidebar-link flex items-center gap-3 px-3.5 py-3 rounded-xl text-white/85 hover:bg-white/10 hover:text-white transition-all duration-200">
                            <span class="sidebar-icon w-6 h-6 shrink-0 flex items-center justify-center">
                                <i class="fa-solid fa-right-from-bracket text-[15px]"></i>
                            </span>
                            <span class="sidebar-text text-sm font-medium">Logout</span>
                        </a>
                    </div>
                </aside>

                <div class="flex-1 flex flex-col min-w-0 md:ml-64 lg:ml-64 2xl:ml-0 2xl:h-full 2xl:min-h-0 bg-gray-200">
                    <header
                        class="flex items-center justify-between md:justify-end px-4 sm:px-6 lg:px-8 py-4 sm:py-6 fixed 2xl:static top-0 left-0 right-0 z-40 shadow-2xs shadow-gray-300 bg-white 2xl:shrink-0 2xl:w-full">
                        <button id="openSidebar"
                            class="md:hidden w-9 h-9 flex items-center justify-center text-gray-600 hover:text-[#B70F1D] hover:bg-gray-100 rounded-lg transition-colors"
                            aria-label="Open sidebar">
                            <i class="fa-solid fa-bars text-lg"></i>
                        </button>
                        <div class="flex items-center gap-2 sm:gap-3 md:gap-4 lg:gap-5 xl:gap-6">
                            <div class="relative" id="profileWrapper">
                                <button id="profileButton" type="button"
                                    class="flex items-center gap-1.5 sm:gap-2 md:gap-2.5 lg:gap-3 cursor-pointer outline-none rounded-full border border-gray-300 bg-white pl-1.5 pr-3 sm:pl-2 sm:pr-4 py-1 sm:py-1.5"
                                    aria-label="Admin profile" aria-expanded="false" aria-controls="profileCard">

                                    @php
                                        $hasHeaderImage = Auth::user()->image && file_exists(public_path('admin_assets/images/' . Auth::user()->image));
                                    @endphp

                                    <div
                                        class="w-7 h-7 sm:w-8 sm:h-8 md:w-9 md:h-9 lg:w-10 lg:h-10 xl:w-10 xl:h-10 rounded-full bg-gray-200 flex items-center justify-center overflow-hidden text-gray-700 shrink-0">

                                        {{-- Uploaded avatar --}}
                                        <img
                                            src="{{ $hasHeaderImage ? asset('admin_assets/images/' . Auth::user()->image) : '' }}"
                                            alt="{{ Auth::user()->name ?? 'Admin' }}"
                                            style="display: {{ $hasHeaderImage ? 'block' : 'none' }};"
                                            class="w-full h-full object-cover" />

                                        {{-- Default placeholder icon --}}
                                        <i class="fa-solid fa-user text-[11px] sm:text-xs md:text-sm lg:text-sm" style="display: {{ $hasHeaderImage ? 'none' : 'block' }};"></i>
                                    </div>

                                    <span class="hidden sm:flex flex-col items-start leading-tight">
                                        <span class="text-xs md:text-sm lg:text-sm xl:text-base font-semibold text-gray-800 whitespace-nowrap">
                                            {{ Auth::user()->name ? ucfirst(Auth::user()->name) : 'N/A' }}
                                        </span>
    
                                        <span class="text-[10px] md:text-[11px] lg:text-xs text-gray-400 whitespace-nowrap">
                                            {{ Auth::user()->role_name ?? 'Admin' }}
                                        </span>
                                    </span>

                                    <i id="profileArrow"
                                        class="fa-solid fa-chevron-down text-[9px] sm:text-[10px] md:text-xs text-gray-700 transition-transform duration-200">
                                    </i>
                                </button>

                                <div id="profileCard"
                                    class="hidden absolute right-0 top-[calc(100%+8px)] sm:top-[calc(100%+10px)] md:top-[calc(100%+12px)] w-[calc(100vw-1.5rem)] max-w-[250px] sm:w-[270px] md:w-[290px] lg:w-[300px] xl:w-[310px] 2xl:w-[320px] bg-white rounded-xl sm:rounded-2xl border border-gray-100 shadow-[0_12px_35px_rgba(0,0,0,0.12)] overflow-hidden z-[100]">
                                    <div
                                        class="absolute -top-2 right-4 sm:right-5 md:right-6 w-4 h-4 bg-white border-l border-t border-gray-100 rotate-45">
                                    </div>
                                    <div class="relative p-1.5 sm:p-2 md:p-2.5">
                                        <a href="{{ route('admin.profile') }}"
                                            class="flex items-center gap-2 sm:gap-3 px-2.5 py-2 sm:px-3 sm:py-2.5 md:py-3 rounded-lg text-gray-600 hover:bg-gray-50 hover:text-[#B70F1D] transition-colors">

                                            <span
                                                class="w-7 h-7 sm:w-8 sm:h-8 shrink-0 flex items-center justify-center text-gray-400">
                                                <i class="fa-regular fa-user text-xs sm:text-sm"></i>
                                            </span>
                                            <span class="text-xs sm:text-sm md:text-sm font-medium">
                                                Profile
                                            </span>
                                        </a>

                                        <a href="{{ route('admin.password') }}"
                                            class="flex items-center gap-2 sm:gap-3 px-2.5 py-2 sm:px-3 sm:py-2.5 md:py-3 rounded-lg text-gray-600 hover:bg-gray-50 hover:text-[#B70F1D] transition-colors">
                                            <span
                                                class="w-7 h-7 sm:w-8 sm:h-8 shrink-0 flex items-center justify-center text-gray-400">
                                                <i class="fa-solid fa-lock text-xs sm:text-sm">
                                                </i>
                                            </span>
                                            <span class="text-xs sm:text-sm md:text-sm font-medium break-words">
                                                Update Password
                                            </span>
                                        </a>
                                    </div>
                                    <div class="mx-3 sm:mx-4 md:mx-5 border-t border-gray-100">
                                    </div>
                                    <div class="p-1.5 sm:p-2 md:p-2.5">
                                        <a href="{{ route('admin.logout') }}"
                                            class="flex items-center gap-2 sm:gap-3 px-2.5 py-2 sm:px-3 sm:py-2.5 md:py-3 rounded-lg text-[#B70F1D] hover:bg-[#B70F1D]/5 transition-colors">
                                            <span class="w-7 h-7 sm:w-8 sm:h-8 shrink-0 flex items-center justify-center">
                                                <i class="fa-solid fa-right-from-bracket text-xs sm:text-sm"></i>
                                            </span>
                                            <span class="text-xs sm:text-sm md:text-sm font-semibold">Logout</span>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </header>
                    <main class="flex-1 pb-10 mt-26 2xl:mt-0 bg-gray-200 2xl:overflow-y-auto 2xl:min-h-0">
                        @yield('content')
                    </main>
                </div>
            </div>
        </div>
    </div>

    <script>

        // Sidebar Menu
        const sidebar = document.getElementById("sidebar");
        const openSidebar = document.getElementById("openSidebar");
        const closeSidebar = document.getElementById("closeSidebar");
        const sidebarOverlay = document.getElementById("sidebarOverlay");

        function openSidebarMenu() {
            sidebar.classList.remove("-translate-x-full");
            sidebarOverlay.classList.remove("hidden");
        }

        function closeSidebarMenu() {
            sidebar.classList.add("-translate-x-full");
            sidebarOverlay.classList.add("hidden");
        }

        openSidebar.addEventListener("click", openSidebarMenu);
        closeSidebar.addEventListener("click", closeSidebarMenu);
        sidebarOverlay.addEventListener("click", closeSidebarMenu);




        //Files Drop Down
        document.addEventListener("DOMContentLoaded", function () {

            const filesDropdownBtn = document.getElementById("filesDropdownBtn");
            const filesDropdown = document.getElementById("filesDropdown");
            const filesArrow = document.getElementById("filesArrow");

            if (!filesDropdownBtn || !filesDropdown || !filesArrow) {
                return;
            }

            const currentPage = window.location.pathname.split("/").pop().toLowerCase();

            const filePages = ["reels.html", "images.html", "documents.html"];

            function openFilesDropdown() {
                filesDropdown.classList.remove("hidden");
                filesArrow.classList.add("rotate-180");
                filesDropdownBtn.setAttribute("aria-expanded", "true");
            }

            function closeFilesDropdown() {
                filesDropdown.classList.add("hidden");
                filesArrow.classList.remove("rotate-180");
                filesDropdownBtn.setAttribute("aria-expanded", "false");
            }

            if (filePages.includes(currentPage)) {
                openFilesDropdown();
            }

            filesDropdownBtn.addEventListener("click", function () {
                const isOpen = !filesDropdown.classList.contains("hidden");

                if (isOpen) {
                    closeFilesDropdown();
                } else {
                    openFilesDropdown();
                }
            });
        });

        // Profile DropDown
        const profileButton = document.getElementById("profileButton");
        const profileCard = document.getElementById("profileCard");
        const profileArrow = document.getElementById("profileArrow");

        if (profileButton && profileCard) {
            profileButton.addEventListener("click", (event) => {
                event.stopPropagation();

                const isOpen = !profileCard.classList.contains("hidden");

                if (isOpen) {
                    profileCard.classList.add("hidden");
                    profileArrow?.classList.remove("rotate-180");
                    profileButton.setAttribute("aria-expanded", "false");
                } else {
                    profileCard.classList.remove("hidden");
                    profileArrow?.classList.add("rotate-180");
                    profileButton.setAttribute("aria-expanded", "true");
                }
            });

            profileCard.addEventListener("click", (event) => {
                event.stopPropagation();
            });

            document.addEventListener("click", () => {
                profileCard.classList.add("hidden");
                profileArrow?.classList.remove("rotate-180");
                profileButton.setAttribute("aria-expanded", "false");
            });
        }
    </script>
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

    @stack('scripts')

</body>


</html>