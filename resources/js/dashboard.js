$(document).ready(function() {
    const rows = $('table tbody tr');
    const entriesDropdown = $('#entries');
    const searchInput = $('#search');
    const prevButton = $('.prev');
    const nextButton = $('.next');
    const currentPageSpan = $('.current-page');

    let currentPage = 1;
    let rowsPerPage = parseInt(entriesDropdown.val());

    function updateTable() {
        const searchTerm = searchInput.val().toLowerCase();
        const filteredRows = rows.filter(function() {
            const text = $(this).text().toLowerCase();
            return text.includes(searchTerm);
        });

        const totalRows = filteredRows.length;
        const startIndex = (currentPage - 1) * rowsPerPage;
        const endIndex = startIndex + rowsPerPage;

        rows.hide();
        filteredRows.slice(startIndex, endIndex).show();

        updatePagination(totalRows);
    }

    function updatePagination(totalRows) {
        const totalPages = Math.ceil(totalRows / rowsPerPage);
        prevButton.prop('disabled', currentPage === 1);
        nextButton.prop('disabled', currentPage === totalPages || totalPages === 0);
        currentPageSpan.text(currentPage);
    }

    entriesDropdown.on('change', function() {
        rowsPerPage = parseInt($(this).val());
        currentPage = 1;
        updateTable();
    });

    searchInput.on('keyup', function() {
        currentPage = 1;
        updateTable();
    });

    prevButton.on('click', function() {
        if (currentPage > 1) {
            currentPage--;
            updateTable();
        }
    });

    nextButton.on('click', function() {
        const totalRows = rows.filter(function() {
            return $(this).is(':visible');
        }).length;
        const totalPages = Math.ceil(totalRows / rowsPerPage);
        if (currentPage < totalPages) {
            currentPage++;
            updateTable();
        }
    });

    // Initialize table
    updateTable();
});
