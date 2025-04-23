<link rel="stylesheet" href="stylesheets/classMessaging/classModal.css">
<div id="add-class-modal" class="modal">
    <div class="modal-content">
        <span class="close-button">&times;</span>
        <h3>Add Class to Menu</h3>
        <div class="class-filter">
            <input type="text" id="class-search" placeholder="Search classes...">
        </div>
        <div class="classes-container">
            <ul id="available-classes">
                <!-- Classes will be loaded here -->
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
    $(document).ready(function() {
        // Class add/remove functionality
        let currentPage = 1;
        let totalPages = 1;
        let searchQuery = '';
        const limitPerPage = 10;

        // Store IDs of classes that are already in the menu
        const existingClassIds = Array.from(document.querySelectorAll('.class-entry'))
            .map(item => parseInt(item.dataset.id));

        // Open modal when add class button is clicked
        $('#add-class-btn').on('click', function() {
            $('#add-class-modal').css({
                display: 'flex'
            });

            // Load classes when modal opens
            loadAvailableClasses(1);
        });

        // Close modal when X is clicked
        $('.close-button').on('click', function() {
            $('#add-class-modal').css({
                display: 'none'
            });
        });

        // Close modal when clicking outside
        $(window).on('click', function(event) {
            if ($(event.target).is('#add-class-modal')) {
                $('#add-class-modal').css({
                    display: 'none'
                });
            }
        });

        // Handle pagination
        $('#prev-page').on('click', function() {
            if (currentPage > 1) {
                loadAvailableClasses(currentPage - 1);
            }
        });

        $('#next-page').on('click', function() {
            if (currentPage < totalPages) {
                loadAvailableClasses(currentPage + 1);
            }
        });

        // Handle search input
        let searchTimeout;
        $('#class-search').on('input', function() {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(() => {
                searchQuery = $(this).val().trim();
                loadAvailableClasses(1); // Reset to page 1 when searching
            }, 300);
        });

        // Function to load available classes
        function loadAvailableClasses(page) {
            currentPage = page;
            const offset = (page - 1) * limitPerPage;

            // Show loading state
            $('#available-classes').html('<div class="loading">Loading classes...</div>');

            // Construct the URL with pagination parameters
            let url = `http://localhost/classes?limit=${limitPerPage}&offset=${offset}`;

            // Add search parameter if present
            if (searchQuery) {
                url += `&search=${encodeURIComponent(searchQuery)}`;
            }

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
                            totalPages = Math.ceil(response.pagination.total / limitPerPage);
                            $('#page-indicator').text(`Page ${currentPage} of ${totalPages}`);
                            $('#prev-page').prop('disabled', currentPage <= 1);
                            $('#next-page').prop('disabled', currentPage >= totalPages);
                        }
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
            }

            let html = '';
            classes.forEach(classItem => {
                // Check if the class is already in the menu
                const isAdded = existingClassIds.includes(classItem.ClassID);

                html += `
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
                </li>
            `;
            });

            $('#available-classes').html(html);

            // Add event listener to add buttons
            $('.add-button:not(.added)').on('click', function() {
                const button = $(this);
                const classId = button.data('id');
                const subject = button.data('subject');
                const level = button.data('level');
                const group = button.data('group');

                // Add the class to the menu
                addClassToMenu(classId, subject, level, group);

                // Update button state
                button.addClass('added').prop('disabled', true).text('Added');

                // Add to existingClassIds array
                existingClassIds.push(classId);
            });
        }

        // Function to add a class to the menu
        function addClassToMenu(classId, subjectCode, level, group) {
            // Create the new class entry
            const newClass = $(`
            <li class="class-entry"
                data-id="${classId}"
                data-name="${subjectCode}"
                data-level="${level}"
                data-group="${group}"
                data-subject="${subjectCode}">
                ${subjectCode} ${group[0]}${level}
            </li>
        `);

            // Add click event handler to the new class entry
            newClass.on('click', function() {
                const classID = $(this).data('id');
                const subject = $(this).data('subject');
                const level = $(this).data('level');
                const group = $(this).data('group');
                const name = $(this).data('name');

                // Trigger class message & member loading
                loadMembersForClass(classID);
                loadMessagesForClass(classID);

                // Set hidden input value
                $("#classID").val(classID);

                // Highlight selected class
                $('.class-entry').removeClass('active');
                $(this).addClass('active');
                $('#classChat-description').text(`${name || 'Class'} (Level ${level || '?'} - Group ${group || '?'})`);

                // Show interface elements
                $('#classChat-cover').css({
                    display: 'none',
                    opacity: 1
                }).animate({
                    opacity: 0
                }, 100);
                $('#classChat-header').css({
                    display: 'flex',
                    opacity: 0
                }).animate({
                    opacity: 1
                }, 300);
                $('#classChat-body').css({
                    display: 'flex',
                    opacity: 0
                }).animate({
                    opacity: 1
                }, 200);
                $('#classChat-footer').css({
                    display: 'flex',
                    opacity: 0
                }).delay(100).animate({
                    opacity: 1
                }, 300);
            });

            // Append to the menu
            $('#class-menu').append(newClass);

            // Add a brief highlight effect
            newClass.addClass('highlight');
            setTimeout(() => {
                newClass.removeClass('highlight');
            }, 1500);
        }

        // Add highlight effect CSS
        $('<style>')
            .prop('type', 'text/css')
            .html(`
            @keyframes highlightNew {
                0% { background-color: rgba(76, 175, 80, 0.5); }
                100% { background-color: rgba(150, 150, 255, 0.2); }
            }
            .class-entry.highlight {
                animation: highlightNew 1.5s ease-out;
            }
        `)
            .appendTo('head');
    });
</script>