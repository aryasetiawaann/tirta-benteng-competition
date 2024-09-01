import "./bootstrap";

document.addEventListener("click", (event) => {
    const burger = document.querySelector(".nav-burger");
    const close = document.querySelector(".sidebar-close");
    const sidebar = document.querySelector(".sidebar");

    if (burger && burger.contains(event.target)) {
        if (sidebar.style.display === "block") {
            sidebar.style.display = "none";
        } else {
            sidebar.style.display = "block";
        }
    } else if (close && close.contains(event.target)) {
        sidebar.style.display = "none";
    } else if (!sidebar.contains(event.target)) {
        sidebar.style.display = "none";
    }
});

// Buat ubah warna navbar
document.addEventListener("scroll", () => {
    const navbar = document.querySelector(".navbar");
    const scrollHeight = window.innerHeight - navbar.offsetHeight;

    if (window.scrollY >= scrollHeight) {
        navbar.classList.add("scrolled");
    } else {
        navbar.classList.remove("scrolled");
    }
});

document.addEventListener('DOMContentLoaded', function () {
    let currentIndex = 0;
    const kompetisiContainers = document.querySelectorAll('.jadwal-container');
    const totalKompetisi = kompetisiContainers.length;

    document.getElementById('jadPrevBtn').addEventListener('click', function () {
        kompetisiContainers[currentIndex].style.display = 'none';
        currentIndex = (currentIndex - 1 + totalKompetisi) % totalKompetisi;
        kompetisiContainers[currentIndex].style.display = 'block';
    });

    document.getElementById('jadNextBtn').addEventListener('click', function () {
        kompetisiContainers[currentIndex].style.display = 'none';
        currentIndex = (currentIndex + 1) % totalKompetisi;
        kompetisiContainers[currentIndex].style.display = 'block';
    });
});
