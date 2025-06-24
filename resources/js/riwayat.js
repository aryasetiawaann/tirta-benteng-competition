document.addEventListener('DOMContentLoaded', function() {
    // Filter kejuaraan cards based on year
    const yearFilter = document.getElementById('year-filter');
    if (yearFilter) {
        yearFilter.addEventListener('change', function() {
            const selectedYear = this.value;
            const cards = document.querySelectorAll('.kejuaraan-card');
            
            cards.forEach(card => {
                const cardYear = card.getAttribute('data-year');
                
                if (selectedYear === 'all' || cardYear === selectedYear) {
                    card.style.display = 'flex';
                    setTimeout(() => {
                        card.classList.add('visible');
                    }, 10);
                } else {
                    card.classList.remove('visible');
                    setTimeout(() => {
                        card.style.display = 'none';
                    }, 300); // Match this with your CSS transition time
                }
            });
        });
    }
    
    // Search functionality
    const searchInput = document.getElementById('search-input');
    if (searchInput) {
        searchInput.addEventListener('input', function() {
            const searchTerm = this.value.toLowerCase();
            const cards = document.querySelectorAll('.kejuaraan-card');
            
            cards.forEach(card => {
                const cardTitle = card.querySelector('.kejuaraan-title').textContent.toLowerCase();
                const cardLocation = card.querySelector('.kejuaraan-location').textContent.toLowerCase();
                
                if (cardTitle.includes(searchTerm) || cardLocation.includes(searchTerm)) {
                    card.style.display = 'flex';
                    setTimeout(() => {
                        card.classList.add('visible');
                    }, 10);
                } else {
                    card.classList.remove('visible');
                    setTimeout(() => {
                        card.style.display = 'none';
                    }, 300); // Match this with your CSS transition time
                }
            });
        });
    }
    
    // Certificate detail page animation
    const certificateCard = document.querySelector('.certificate-card');
    if (certificateCard) {
        setTimeout(() => {
            certificateCard.classList.add('animated');
        }, 100);
    }
});
