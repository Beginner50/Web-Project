<div id="membersModal" class="modal" style="display:none;">
    <div class="modal-content">
        <span class="close">&times;</span>
        <h2>Class Members</h2>
        <div class="modal-body"></div>
    </div>
</div>

<script>
    $(document).ready(function() {
        // View members modal
        $('#viewMembers-button').on('click', function() {
            if (!selectedClassID) {
                alert("Please select a class first.");
                return;
            }

            $.ajax({
                url: `/classes/${selectedClassID}/members`,
                method: 'GET',
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        const students = response.data.filter(u => u.UserType === 'Student');
                        const teachers = response.data.filter(u => u.UserType === 'Teacher');

                        const $modalBody = $('#membersModal .modal-body');
                        $modalBody.empty();

                        if (teachers.length > 0) {
                            $modalBody.append('<h3>Teachers</h3>');
                            teachers.forEach(user => {
                                $modalBody.append(`<div class="member teacher">ID: ${user.UserID}</div>`);
                            });
                        }

                        if (students.length > 0) {
                            $modalBody.append('<h3>Students</h3>');
                            students.forEach(user => {
                                $modalBody.append(`<div class="member student">ID: ${user.UserID}</div>`);
                            });
                        }

                        $('#membersModal').fadeIn();
                    } else {
                        alert("Could not retrieve members.");
                    }
                },
                error: function() {
                    alert("An error occurred while fetching class members.");
                }
            });
        });

        // Close modal
        $('#membersModal .close').on('click', function() {
            $('#membersModal').fadeOut();
        });
    });
</script>