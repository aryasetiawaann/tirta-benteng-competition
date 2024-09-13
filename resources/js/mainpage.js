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


    const prevBtn = document.getElementById('jadPrevBtn');
    const nextBtn = document.getElementById('jadNextBtn');


    if(prevBtn || nextBtn){

        prevBtn.addEventListener('click', function () {
            kompetisiContainers[currentIndex].style.display = 'none';
            currentIndex = (currentIndex - 1 + totalKompetisi) % totalKompetisi;
            kompetisiContainers[currentIndex].style.display = 'block';
        });
    
        nextBtn.addEventListener('click', function () {
            kompetisiContainers[currentIndex].style.display = 'none';
            currentIndex = (currentIndex + 1) % totalKompetisi;
            kompetisiContainers[currentIndex].style.display = 'block';
        });
    }

    const eventItems = document.querySelectorAll('.event-list li');
    const priceSections = document.querySelectorAll('.price');

    eventItems.forEach(item => {
        item.addEventListener('click', function() {
            // Remove active-event class from all event items
            eventItems.forEach(event => event.classList.remove('active-event'));

            // Hide all price sections
            priceSections.forEach(price => price.style.display = 'none');

            // Add active-event class to the clicked item
            this.classList.add('active-event');

            // Get the index of the clicked event
            const index = this.getAttribute('data-index');

            // Show all price sections corresponding to the clicked event
            document.querySelectorAll(`[id^="price-${index}"]`).forEach(price => {
                price.style.display = '';
            });
        });
    });


    document.getElementById('termsLink').addEventListener('click', function() {
        document.getElementById('termsOverlay').style.display = 'flex';
    });

    document.getElementById('closeOverlay').addEventListener('click', function() {
        document.getElementById('termsOverlay').style.display = 'none';
    });


    window.onclick = function(event) {
        const overlay = document.getElementById('termsOverlay');
        if (event.target == overlay) {
            overlay.style.display = 'none';
        }
    }
});