$('#openOverlay').click(function() {
    $('#overlay').css('display', 'flex');
});

$('#closeOverlay').click(function() {
    $('#overlay').css('display', 'none');
});


$(document).ready(function() {
    const rows = $('table tbody tr');
    const entriesDropdown = $('#entries');
    const searchInput = $('#search');
    const prevButton = $('.prev');
    const nextButton = $('.next');
    const pageNumbersDiv = $('.page-numbers');

    let currentPage = 1;
    let rowsPerPage = parseInt(entriesDropdown.val());

    function updateTable() {
        const searchTerm = searchInput.val().toLowerCase();
        const filteredRows = rows.filter(function() {
            const text = $(this).text().toLowerCase();
            return text.includes(searchTerm);
        });

        const totalRows = filteredRows.length;
        const totalPages = Math.ceil(totalRows / rowsPerPage);

        // Update pagination visibility
        prevButton.prop('disabled', currentPage === 1);
        nextButton.prop('disabled', currentPage === totalPages);

        // Hide all rows and show only the current page's rows
        rows.hide();
        filteredRows.slice((currentPage - 1) * rowsPerPage, currentPage * rowsPerPage).show();

        // Update page numbers
        updatePagination(totalPages);
    }

    function updatePagination(totalPages) {
        pageNumbersDiv.empty();

        for (let i = 1; i <= totalPages; i++) {
            const pageNumber = $('<span class="page-number"></span>').text(i);

            if (i === currentPage) {
                pageNumber.addClass('current');
            }

            pageNumber.on('click', function() {
                currentPage = i;
                updateTable();
            });

            pageNumbersDiv.append(pageNumber);
        }
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
        const searchTerm = searchInput.val().toLowerCase();
        const filteredRows = rows.filter(function() {
            const text = $(this).text().toLowerCase();
            return text.includes(searchTerm);
        });
        const totalRows = filteredRows.length;
        const totalPages = Math.ceil(totalRows / rowsPerPage);

        if (currentPage < totalPages) {
            currentPage++;
            updateTable();
        }
    });

    // Initialize table
    updateTable();
});
