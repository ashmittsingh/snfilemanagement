const notificationButton = document.getElementById("notificationButton");
const notificationCard = document.getElementById("notificationCard");
const notificationBadge = document.getElementById("notificationBadge");
const markAllRead = document.getElementById("markAllRead");
const notificationItems = document.querySelectorAll(".notification-item");
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

const openNotifications = () => {
    notificationCard.classList.remove("hidden");
    notificationButton.setAttribute("aria-expanded", "true");
};

const closeNotifications = () => {
    notificationCard.classList.add("hidden");
    notificationButton.setAttribute("aria-expanded", "false");
};

notificationButton.addEventListener("click", (event) => {
    event.stopPropagation();
    const isOpen = !notificationCard.classList.contains("hidden");
    if (isOpen) {
        closeNotifications();
    } else {
        openNotifications();
    }
});

notificationCard.addEventListener("click", (event) => {
    event.stopPropagation();
});

document.addEventListener("click", () => {
    closeNotifications();
});

markAllRead?.addEventListener("click", () => {
    notificationItems.forEach((item) => {
        item.classList.add("opacity-60");

        const dot = item.querySelector("span:first-child");
        if (dot) {
            dot.classList.remove("bg-[#B70F1D]");
            dot.classList.add("bg-gray-300");
        }
    });

    if (notificationBadge) {
        notificationBadge.textContent = "0";
        notificationBadge.classList.add("hidden");
    }
});

notificationItems.forEach((item) => {
    item.addEventListener("click", () => {
        item.classList.add("opacity-60");

        const dot = item.querySelector("span:first-child");
        if (dot) {
            dot.classList.remove("bg-[#B70F1D]");
            dot.classList.add("bg-gray-300");
        }
    });
});

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