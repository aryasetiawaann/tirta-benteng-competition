document.addEventListener('DOMContentLoaded', function () {
    // Handle error and success messages
    handleMessages('error-list');
    handleMessages('success-list');

    // Handle window resize
    handleResize();
    window.addEventListener('resize', handleResize);

    // Handle overlay functionality
    setupOverlay('openOverlay', 'overlay');
    setupOverlay('openOverlay2', 'overlay2');
    closeOverlay('closeOverlay', 'overlay');
    closeOverlayOnClickOutside('overlay', '.overlay-container');

    // Initialize table functionality
    if (document.querySelector('table')) {
        initializeTable();
    }
});

// Function to handle error and success messages
function handleMessages(elementId) {
    const messageList = document.getElementById(elementId);
    if (!messageList) return;

    setTimeout(() => messageList.classList.add('show'), 100);
    setTimeout(() => messageList.classList.remove('show'), 10000);

    // Hide the message list when clicking outside of it
    document.addEventListener('click', function (event) {
        if (!messageList.contains(event.target)) {
            messageList.classList.remove('show');
        }
    });
}

// Function to handle window resize
function handleResize() {
    const windowWidth = window.innerWidth;
    const mainContent = document.querySelector('.main-content');
    const sidebar = document.querySelector('.sidebar');

    if (!mainContent || !sidebar) return;

    if (windowWidth <= 1024) {
        mainContent.classList.add('main-content_sidebar-hide');
        sidebar.classList.add('active');
    } else {
        mainContent.classList.remove('main-content_sidebar-hide');
        sidebar.classList.remove('active');
    }

    if (windowWidth <= 768) {
        mainContent.classList.remove('main-content_sidebar-hide');
        sidebar.classList.remove('active');
    }
}

// Function to set up overlay functionality
function setupOverlay(openButtonId, overlayId) {
    const openButton = document.getElementById(openButtonId);
    const overlay = document.getElementById(overlayId);

    if (!openButton || !overlay) return;

    openButton.addEventListener('click', function () {
        window.scrollTo(0, 0);
        overlay.style.display = 'flex';
        document.body.style.overflow = 'hidden';
    });
}

// Function to close overlay
function closeOverlay(closeButtonId, overlayId) {
    const closeButton = document.getElementById(closeButtonId);
    const overlay = document.getElementById(overlayId);

    if (!closeButton || !overlay) return;

    closeButton.addEventListener('click', function () {
        overlay.style.display = 'none';
        document.body.style.overflow = 'auto';
    });
}

// Function to close overlay when clicking outside
function closeOverlayOnClickOutside(overlayId, containerSelector) {
    const overlay = document.getElementById(overlayId);
    const container = document.querySelector(containerSelector);

    if (!overlay || !container) return;

    overlay.addEventListener('click', function (event) {
        if (!container.contains(event.target)) {
            overlay.style.display = 'none';
            document.body.style.overflow = 'auto';
        }
    });

    // Prevent overlay from closing when clicking inside the container
    container.addEventListener('click', function (event) {
        event.stopPropagation();
    });
}

function initializeTable() {
    const rows = document.querySelectorAll('table tbody tr');
    const entriesDropdown = document.getElementById('entries');
    const searchInput = document.getElementById('search');
    const prevButton = document.querySelector('.prev');
    const nextButton = document.querySelector('.next');
    const pageNumbersDiv = document.querySelector('.page-numbers');

    let currentPage = 1;
    let rowsPerPage = parseInt(entriesDropdown.value);

    // Function to update the table based on search and pagination
    function updateTable() {
        const searchTerm = searchInput.value.toLowerCase();
        const filteredRows = Array.from(rows).filter(row => {
            const text = row.textContent.toLowerCase();
            return text.includes(searchTerm);
        });

        const totalRows = filteredRows.length;
        const totalPages = Math.ceil(totalRows / rowsPerPage);

        // Update pagination buttons
        prevButton.disabled = currentPage === 1;
        nextButton.disabled = currentPage === totalPages;

        // Hide all rows and show only the current page's rows
        rows.forEach(row => row.style.display = 'none'); // Hide all rows first
        filteredRows.slice((currentPage - 1) * rowsPerPage, currentPage * rowsPerPage).forEach(row => {
            row.style.display = ''; // Show rows for the current page
        });

        // Update page numbers
        updatePagination(totalPages);
    }

    // Function to update pagination numbers dynamically
    function updatePagination(totalPages) {
        pageNumbersDiv.innerHTML = ''; // Clear existing page numbers

        // Always show the first page
        addPageNumber(1);

        // Show ellipsis if there are pages before the current page
        if (currentPage > 3) {
            pageNumbersDiv.appendChild(createEllipsis());
        }

        // Show current page and its neighbors
        for (let i = Math.max(2, currentPage - 1); i <= Math.min(totalPages - 1, currentPage + 1); i++) {
            addPageNumber(i);
        }

        // Show ellipsis if there are pages after the current page
        if (currentPage < totalPages - 2) {
            pageNumbersDiv.appendChild(createEllipsis());
        }

        // Always show the last page if there is more than one page
        if (totalPages > 1) {
            addPageNumber(totalPages);
        }
    }

    // Helper function to add a page number
    function addPageNumber(page) {
        const pageNumber = document.createElement('span');
        pageNumber.className = 'page-number';
        pageNumber.textContent = page;

        if (page === currentPage) {
            pageNumber.classList.add('current'); // Highlight the current page
        }

        pageNumber.addEventListener('click', function () {
            currentPage = page; // Update the current page
            updateTable(); // Refresh the table
        });

        pageNumbersDiv.appendChild(pageNumber);
    }

    // Helper function to create an ellipsis
    function createEllipsis() {
        const ellipsis = document.createElement('span');
        ellipsis.className = 'ellipsis';
        ellipsis.textContent = '…';
        return ellipsis;
    }

    // Event listeners
    entriesDropdown.addEventListener('change', function () {
        rowsPerPage = parseInt(this.value); // Update rows per page
        currentPage = 1; // Reset to the first page
        updateTable(); // Refresh the table
    });

    searchInput.addEventListener('keyup', function () {
        currentPage = 1; // Reset to the first page when searching
        updateTable(); // Refresh the table
    });

    prevButton.addEventListener('click', function () {
        if (currentPage > 1) {
            currentPage--; // Go to the previous page
            updateTable(); // Refresh the table
        }
    });

    nextButton.addEventListener('click', function () {
        const searchTerm = searchInput.value.toLowerCase();
        const filteredRows = Array.from(rows).filter(row => {
            const text = row.textContent.toLowerCase();
            return text.includes(searchTerm);
        });
        const totalPages = Math.ceil(filteredRows.length / rowsPerPage);

        if (currentPage < totalPages) {
            currentPage++; // Go to the next page
            updateTable(); // Refresh the table
        }
    });

    // Initialize table on page load
    updateTable();
}