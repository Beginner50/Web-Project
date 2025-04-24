<link rel="stylesheet" href="stylesheets/authentication/subjectModal.css">
<div id="add-subject-modal" class="modal" style="display: none;">
    <div class="modal-content">
        <span class="close-button">&times;</span>
        <h3>Select Subject</h3>
        <div class="subjects-container">
            <ul id="available-subjects">
                <!-- Subjects will be loaded here -->
                <div class="loading">Loading subjects...</div>
            </ul>
        </div>
        <div class="modal-pagination">
            <input id="total-pages" hidden>
            <button id="subject-prev-page" disabled>&laquo; Previous</button>
            <span id="subject-page-indicator">Page 1</span>
            <button id="subject-next-page">Next &raquo;</button>
        </div>
    </div>
</div>

<script>
    // Open modal function (you need to call this when you want to open the subject modal)
    function openSubjectModal() {
        $('#add-subject-modal').css({
            display: 'flex'
        });
        loadAvailableSubjects(1);
    }

    // Function to load available subjects
    function loadAvailableSubjects(page) {
        const offset = (page - 1) * 5;

        // Show loading state
        $('#available-subjects').html('<div class="loading">Loading subjects...</div>');
        $('#subject-page-indicator').hide(0);
        $('#subject-prev-page').hide(0);
        $('#subject-next-page').hide(0);

        // Get & Display subjects
        $.ajax({
            url: `http://localhost/subjects?limit=5&offset=${offset}`,
            method: 'GET',
            dataType: 'json',
            success: function(response) {
                if (response.success && response.data) {
                    displayAvailableSubjects(response.data);

                    $('#subject-page-indicator').show(0);
                    $('#subject-prev-page').show(0);
                    $('#subject-next-page').show(0);

                    // Update pagination
                    if (response.pagination) {
                        const subjectTotalPages = Math.ceil(response.pagination.total / 5);
                        $('#total-pages').val(subjectTotalPages);
                        $('#subject-page-indicator').text(`Page ${page} of ${subjectTotalPages}`);
                        $('#subject-prev-page').prop('disabled', page <= 1);
                        $('#subject-next-page').prop('disabled', page >= subjectTotalPages);
                    }
                } else {
                    $('#available-subjects').html('<div class="loading">No subjects found.</div>');
                }
            },
            error: function() {
                $('#available-subjects').html('<div class="loading">Error loading subjects. Please try again.</div>');
            }
        });
    }

    // Function to display available subjects
    function displayAvailableSubjects(subjects) {
        if (!subjects.length) {
            $('#available-subjects').html('<div class="loading">No subjects found.</div>');
            return;
        }

        let html = '';
        subjects.forEach(subject => {
            const selectedSubjects = JSON.parse($('#selected-subjects').val());
            const isSelected = selectedSubjects.includes(subject.SubjectCode);

            html += `
                <li class="subject-option">
                    <div class="subject-details">
                        <span class="subject-code">${subject.SubjectCode}</span> - 
                        <span class="subject-name">${subject.SubjectName}</span>
                    </div>
                    <button class="select-button ${isSelected ? 'selected' : ''}" 
                            ${isSelected ? 'disabled' : ''} 
                            data-code="${subject.SubjectCode}" 
                            data-name="${subject.SubjectName}">
                        ${isSelected ? 'Selected' : 'Select'} 
                    </button>
                </li>
            `;
        });

        $('#available-subjects').html(html);

        // Add event listener to select buttons
        $('.select-button:not(.selected)').on('click', function() {
            const button = $(this);
            const subjectCode = button.data('code');
            const subjectName = button.data('name');

            // Keep track of subjects added
            let selectedSubjects = JSON.parse($('#selected-subjects').val());
            if (selectedSubjects == null) selectedSubjects = [];
            selectedSubjects.push(subjectCode);
            $('#selected-subjects').val(JSON.stringify(selectedSubjects));

            // Add the subject to the list & move addSubject-button 
            const addSubjectButton = $("#addSubject-button").detach();
            $("#subject-list").append(`
                    <div class="subject"
                        data-id="${subjectCode}"
                        data-name="${subjectName}">
                         ${subjectCode}
                        <img class="remove-subject" src="assets/backspace.svg"> </img>
                    </div>
                `);
            addSubjectButton.appendTo("#subject-list");

            // Close the modal
            $('#add-subject-modal').css({
                display: 'none'
            });
        });
    }

    $(document).ready(function() {
        let subjectCurrentPage = 1;

        // Close modal when X is clicked
        $('#add-subject-modal .close-button').on('click', function() {
            subjectCurrentPage = 1;
            $('#add-subject-modal').css({
                display: 'none'
            });
        });

        // Close modal when clicking outside
        $(window).on('click', function(event) {
            if ($(event.target).is('#add-subject-modal')) {
                subjectCurrentPage = 1;
                $('#add-subject-modal').css({
                    display: 'none'
                });
            }
        });

        // Handle pagination
        $('#subject-prev-page').on('click', function() {
            if (subjectCurrentPage > 1) {
                subjectCurrentPage -= 1;
                loadAvailableSubjects(subjectCurrentPage);
            }
        });

        $('#subject-next-page').on('click', function() {
            if (subjectCurrentPage < $('#total-pages').val()) {
                subjectCurrentPage += 1;
                loadAvailableSubjects(subjectCurrentPage);
            }
        });
    });
</script>