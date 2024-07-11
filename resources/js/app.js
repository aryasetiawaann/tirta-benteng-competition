import "./bootstrap";

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
