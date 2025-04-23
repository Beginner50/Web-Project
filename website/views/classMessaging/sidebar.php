<aside class="sidebar">
    <h2>Classes</h2>
    <menu id="class-menu">
        <?php foreach ($classes as $class): ?>
            <li class="class-entry"
                data-id="<?= $class['ClassID'] ?>"
                data-name="<?= htmlspecialchars($class['SubjectName']) ?>"
                data-level="<?= $class['Level'] ?>"
                data-group="<?= $class['ClassGroup'] ?>"
                data-subject="<?= $class['SubjectCode'] ?>">
                <?= htmlspecialchars($class['SubjectName']) . " " . $class["ClassGroup"][0] . $class["Level"] ?>
            </li>
        <?php endforeach; ?>
    </menu>
</aside>

<script>
    $(document).ready(function() {

        $('.class-entry').on('click', function() {
            const classId = $(this).data('id');
            const subject = $(this).data('subject');
            const level = $(this).data('level');
            const group = $(this).data('group');
            const name = $(this).data('name');

            selectedClassID = classId;

            // Highlight selected class
            $('.class-entry').removeClass('active');
            $(this).addClass('active');

            // Update header text immediately
            $('#classChat-description').text(`${name} (Level ${level} - Group ${group})`);

            // Show the chat interface immediately
            $('#classChat-header').css({
                display: 'flex',
                opacity: 0
            }).animate({
                opacity: 1
            }, 300);
            $('#classChat-footer').css({
                display: 'flex',
                opacity: 0
            }).animate({
                opacity: 1
            }, 300);
            $('#classChat-body').css({
                display: 'block',
                opacity: 0
            });

            // Load messages via AJAX right away
            console.log(selectedClassID);
            $.ajax({
                url: `http://localhost/classes/${selectedClassID}/messages`,
                method: 'GET',
                dataType: 'json',
                success: function(response) {
                    if (response.data && response.data.length > 0) {
                        $('#classChat-body').html("");
                        response.data.forEach(msg => {
                            $('#classChat-body').append(`
                        <div class="message user">
                            <div class="msg-text">${msg.Message}</div>
                            <div class="msg-meta">User: ${msg.UserID} • ${msg.DateSent}</div>
                        </div>
                    `);
                        });
                    } else {
                        $('#classChat-body').html(`<div class='message system'>No messages yet for this class.</div>`);
                    }

                    // Fade in message body after messages are loaded
                    $('#classChat-body').animate({
                        opacity: 1
                    }, 300);
                },
                error: function() {
                    $('#classChat-body').append(`<div class='message system error'>Failed to load messages.</div>`);
                    $('#classChat-body').animate({
                        opacity: 1
                    }, 300);
                }
            });

            // Fade out the cover (after the AJAX is already running)
            $('#classChat-cover').fadeOut(300);
        });
    });
</script>