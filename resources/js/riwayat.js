document.addEventListener('DOMContentLoaded', function() {
    // Elements
    const yearFilter = document.getElementById('yearFilter');
    const searchInput = document.getElementById('searchInput');
    const kejuaraanGrid = document.getElementById('kejuaraanGrid');
    const noResults = document.getElementById('noResults');
    const kejuaraanCards = document.querySelectorAll('.kejuaraan-card');

    // Function to filter kejuaraan cards
    function filterKejuaraan() {
        const selectedYear = yearFilter.value.toLowerCase();
        const searchTerm = searchInput.value.toLowerCase();
        let visibleCount = 0;

        kejuaraanCards.forEach(card => {
            const cardYear = card.getAttribute('data-year').toLowerCase();
            const cardTitle = card.getAttribute('data-title').toLowerCase();
            
            const matchesYear = selectedYear === '' || cardYear === selectedYear;
            const matchesSearch = searchTerm === '' || cardTitle.includes(searchTerm);
            
            if (matchesYear && matchesSearch) {
                card.style.display = 'block';
                visibleCount++;
            } else {
                card.style.display = 'none';
            }
        });

        // Show/hide no results message
        if (visibleCount === 0) {
            noResults.style.display = 'block';
        } else {
            noResults.style.display = 'none';
        }
    }

    // Event listeners
    if (yearFilter) {
        yearFilter.addEventListener('change', filterKejuaraan);
    }
    
    if (searchInput) {
        searchInput.addEventListener('input', filterKejuaraan);
    }

    // Initialize animations with delay for smoother page load
    setTimeout(() => {
        document.querySelectorAll('.kejuaraan-card, .category-card').forEach(card => {
            card.style.opacity = '1';
        });
    }, 100);
});
