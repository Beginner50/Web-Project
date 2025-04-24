<link rel="stylesheet" href="stylesheets/classMessaging/classModal.css">
<div id="add-class-modal" class="modal" style="display: none;">
    <div class="modal-content">
        <span class="close-button">&times;</span>
        <h3>Add Class to Menu</h3>
        <input id="total-pages" hidden>
        <div class="classes-container">
            <ul id="available-classes">
                <div class="loading">Loading classes...</div>
            </ul>
        </div>
        <div class="modal-pagination">
            <button id="prev-page" disabled>&laquo; Previous</button>
            <span id="page-indicator">Page 1</span>
            <button id="next-page">Next &raquo;</button>
        </div>
    </div>
</div>

<script>
    // Function to load available classes
    function loadAvailableClasses(page) {
        const offset = (page - 1) * 10;

        // Hide navigation buttons
        $('#page-indicator').hide(0);
        $('#prev-page').hide(0);
        $('#next-page').hide(0);

        // Show loading state
        $('#available-classes').html('<div class="loading">Loading classes...</div>');
        let url = `http://localhost/classes?limit=${10}&offset=${offset}`;

        // Fetch classes from API
        $.ajax({
            url: url,
            method: 'GET',
            dataType: 'json',
            success: function(response) {
                if (response.success && response.data) {
                    displayAvailableClasses(response.data);

                    // Update pagination
                    if (response.pagination) {
                        const totalPages = Math.ceil(response.pagination.total / 10);
                        $('#total-pages').val(totalPages);
                        $('#page-indicator').text(`Page ${page} of ${totalPages}`);
                        $('#prev-page').prop('disabled', page <= 1);
                        $('#next-page').prop('disabled', page >= totalPages);
                    }

                    // Show navigation buttons
                    $('#page-indicator').show(0);
                    $('#prev-page').show(0);
                    $('#next-page').show(0);
                } else {
                    $('#available-classes').html('<div class="loading">No classes found.</div>');
                }
            },
            error: function() {
                $('#available-classes').html('<div class="loading">Error loading classes. Please try again.</div>');
            }
        });
    }

    // Function to display available classes
    function displayAvailableClasses(classes) {
        if (!classes.length) {
            $('#available-classes').html('<div class="loading">No classes found.</div>');
            return;
        } else
            $('#available-classes').html('');

        // Store IDs of classes that are already in the menu
        let existingClassIds = Array.from($('.class-entry'))
            .map(item => parseInt(item.dataset.id));

        classes.forEach(classItem => {
            // Check if the class is already in the menu        
            const isAdded = existingClassIds.includes(classItem.ClassID);

            $('#available-classes').append(`
                <li class="class-option" data-id="${classItem.ClassID}">
                    <div class="class-details">
                        <strong>${classItem.SubjectCode}</strong> - 
                        Level ${classItem.Level}, Group ${classItem.ClassGroup}
                    </div>
                    <button class="add-button ${isAdded ? 'added' : ''}" 
                            ${isAdded ? 'disabled' : ''} 
                            data-id="${classItem.ClassID}" 
                            data-subject="${classItem.SubjectCode}"
                            data-level="${classItem.Level}" 
                            data-group="${classItem.ClassGroup}">
                        ${isAdded ? 'Added' : 'Add'}
                    </button>
                </li>`);
        });

        // Add event listener to add buttons
        $('.add-button:not(.added)').on('click', function() {
            const button = $(this);
            const classID = button.data('id');
            const subject = button.data('subject');
            const level = button.data('level');
            const group = button.data('group');

            // Add the class to the menu
            addClassToMenu(classID, subject, level, group);

            // Update button state
            button.addClass('added').prop('disabled', true).text('Added');

            // Add to existingClassIds array
            existingClassIds.push(classID);
        });
    }

    // Function to add a class to the menu
    function addClassToMenu(classID, subjectCode, level, group) {
        $.ajax({
            url: `http://localhost/classes/${classID}/assign/<?php echo $_SESSION["UserID"] ?>`,
            method: 'POST',
            success: function(response) {
                if (!response.success) {
                    alert("Could not assign class!");
                    return;
                }

                // Create the new class entry
                const newClass = $(`
                <li class="class-entry"
                    data-id="${classID}"
                    data-name="${subjectCode}"
                    data-level="${level}"
                    data-group="${group}"
                    data-subject="${subjectCode}">
                    ${subjectCode} ${group[0]}${level}
                </li> `);

                // Append to the menu
                $('#class-menu').append(newClass);

                // Add a brief highlight effect
                newClass.addClass('highlight');
                setTimeout(() => {
                    newClass.removeClass('highlight');
                }, 1500);
            },
            error: function() {
                alert("Could not assign class!");
            }
        });
    }

    $(document).ready(function() {
        let currentPage = 1;
        loadAvailableClasses(currentPage);

        // Close modal when X is clicked
        $('.close-button').on('click', function() {
            $('#add-class-modal').css({
                display: 'none'
            });
            currentPage = 1;
        });

        // Close modal when clicking outside
        $(window).on('click', function(event) {
            if ($(event.target).is('#add-class-modal')) {
                $('#add-class-modal').css({
                    display: 'none'
                });
                currentPage = 1;
            }
        });

        // Handle pagination
        $('#prev-page').on('click', function() {
            if (currentPage > 1) {
                currentPage = currentPage - 1;
                loadAvailableClasses(currentPage);
            }
        });

        $('#next-page').on('click', function() {
            if (currentPage < $('#total-pages').val()) {
                currentPage = currentPage + 1;
                loadAvailableClasses(currentPage);
            }
        });
    });
</script>