<link rel="stylesheet" href="stylesheets/classMessaging/membersModal.css">
<div id="membersModal" class="modal" style="display:none;">
    <div class="modal-content">
        <span class="close">&times;</span>
        <h2>Class Members</h2>
        <div class="modal-body"></div>
    </div>
</div>

<script>
    function loadMembersForClass(classID) {
        $.ajax({
            url: `/classes/${classID}/members`,
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
                            $modalBody.append(`<div class="member teacher">ID: ${user.UserID}   -   ${user.FirstName} ${user.LastName}</div>`);
                        });
                    }

                    if (students.length > 0) {
                        $modalBody.append('<h3>Students</h3>');
                        students.forEach(user => {
                            $modalBody.append(`<div class="member student">ID: ${user.UserID}   -   ${user.FirstName} ${user.LastName}</div>`);
                        });
                    }

                } else {
                    alert("Could not retrieve members.");
                }
            },
            error: function() {
                alert("An error occurred while fetching class members.");
            }
        });
    }

    $(document).ready(function() {
        // View members modal
        $('#viewMembers-button').on('click', function() {
            $('#membersModal').fadeIn();
        });

        // Close modal
        $('#membersModal .close').on('click', function() {
            $('#membersModal').fadeOut();
        });
    });
</script>