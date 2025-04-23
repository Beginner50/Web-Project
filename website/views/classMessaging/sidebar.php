<link rel="stylesheet" href="stylesheets/classMessaging/sidebar.css">
<div class="class-messaging-sidebar">
    <h2>Classes</h2>
    <?php if ($_SESSION["UserType"] == "Teacher"): ?>
        <button id="add-class-btn" class="add-class-button">+ Add Class</button>
    <?php endif; ?>
    <div class="class-menu-container">
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
    </div>
    <?php require "views/classMessaging/classModal.php" ?>
</div>

<script>
    $(document).ready(function() {
        // --- Class selection logic ---
        $('.class-entry').on('click', function() {
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

            // Highlight selected class (add CSS for .active class)
            $('.class-entry').removeClass('active');
            $(this).addClass('active');
            $('#classChat-description').text(`${name || 'Class'} (Level ${level || '?'} - Group ${group || '?'})`);

            // Show interface elements (using previous fade-in logic)
            $('#classChat-cover').css({
                display: 'none',
                opacity: 1
            }).animate({
                opacity: 0
            }, 100); // Hide the "No Class Selected" cover
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

        // Open modal when add class button is clicked
        $('#add-class-btn').on('click', function() {
            $('#add-class-modal').css({
                display: 'flex'
            });

            // Load classes when modal opens
            loadAvailableClasses(1);
        });
    });
</script>