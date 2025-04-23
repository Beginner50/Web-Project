<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Education Portal Class Tab</title>
    <base href="/website/">
    <link rel="stylesheet" href="stylesheets/partials/sidebar.css">
    <link rel="stylesheet" href="stylesheets/common.css">
    <link rel="stylesheet" href="stylesheets/classMessaging/main.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css">
    <script src="https://code.jquery.com/jquery-3.7.1.min.js" integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
</head>

<body>
    <?php require 'views/partials/navBar.php'; ?>

    <div class="flex-col">
        <?php require "views/classMessaging/sidebar.php" ?>

        <div id="main-wrapper">
            <!-- Chat Header -->
            <div id="classChat-header" class="classChat" style="display: none;">
                <div id="left-section">
                    <img src="assets/schedule-svgrepo-com.svg" alt="Schedule Icon">
                    <div id="classChat-description"></div>
                </div>
                <button id="viewMembers-button" class="indigoTheme roundBorder shadow">
                    View Members
                </button>
            </div>

            <!-- Chat Body -->
            <div id="classChat-body" class="classChat" style="display: none;"></div>

            <!-- Chat Footer -->
            <div id="classChat-footer" class="classChat" style="display: none;">
                <form id="message-form" method="post" action="ClassMessaging/sendMessage.php" style="display: flex; width: 100%; align-items: center;">
                    <textarea name="message-input" id="message-input" placeholder="Input your message here"></textarea>
                    <button id="send-button" type="submit">
                        <img src="assets/send.svg" alt="Send">
                    </button>
                </form>
            </div>


            <!-- Cover Message -->
            <div id="classChat-cover">No Class Selected</div>
        </div>
    </div>

    <?php require "views/classMessaging/membersModal.php" ?>

    <script>
        $(document).ready(function() {
            let selectedClassID = null;

            // Send message form
            $('#message-form').on('submit', function(e) {
                e.preventDefault();
                const msg = $('#message-input').val().trim();

                if (msg) {
                    $('<div>', {
                        class: 'message user',
                        text: msg
                    }).appendTo('#classChat-body');
                    $(this)[0].reset();
                }
            });
        });
    </script>


</body>

</html>