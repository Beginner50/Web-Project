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
            <div id="classChat-header" class="classChat" style="display: none;">
                <div id="left-section">
                    <img src="assets/schedule-svgrepo-com.svg" alt="Schedule Icon">
                    <div id="classChat-description"></div>
                </div>
                <button id="viewMembers-button" class="indigoTheme roundBorder shadow">
                    View Members
                </button>
            </div>

            <input id="classID" value="" hidden>
            <div id="classChat-body" class="classChat" style="display: none;"> </div>

            <div id="classChat-footer" class="classChat" style="display: none;">
                <form id="message-form" method="post" action="#" style="display: flex; width: 100%; align-items: center; gap: 15px;"> <textarea name="message-input" id="message-input" placeholder="Input your message here" style="flex-grow: 1; height: 40px; resize: none;"></textarea> <button id="send-button" type="submit" style="height: 40px; width: 40px; flex-shrink: 0;"> <img src="assets/send.svg" alt="Send">
                    </button>
                </form>
            </div>

            <div id="classChat-cover">
                <div class="cover-text">No Class selected!</div>
            </div>

        </div>
    </div>

    <?php require "views/classMessaging/membersModal.php" ?>

    <script>
        const currentUserID = <?php echo json_encode($_SESSION["UserID"] ?? null); ?>;
        var firstName = [];

        function loadMessagesForClass(classId) {
            const body = $('#classChat-body');
            body.html('<div class="message system">Loading messages...</div>');

            $.ajax({
                url: `http://localhost/classes/${classId}/messages`,
                method: 'GET',
                success: function(response) {
                    if (!response || !response.data) {
                        console.error("Invalid response structure from messages API:", response);
                        body.empty().append(`<div class="message system">Error loading messages (invalid data).</div>`);
                        return;
                    }
                    const messages = response.data;
                    body.empty();
                    if (messages.length === 0) {
                        body.append(`<div class="message system">No messages in this class yet. Be the first!</div>`);
                        return;
                    }

                    messages.forEach(msg => {
                        const senderId = msg.UserID;
                        const senderName = msg.FirstName;
                        const isCurrentUser = (currentUserID !== null && senderId == currentUserID); // Use == for potential type difference if needed, === is stricter

                        const msgDiv = $('<div>')
                            .addClass('message')
                            .addClass(isCurrentUser ? 'sent' : 'received')
                            .html(`
                                <div class="msg-sender">${senderName}</div>
                                <div class="msg-content">${msg.Message}</div>
                                <div class="msg-time">${formatTimestamp(msg.DateSent)}</div>
                            `);
                        body.append(msgDiv);
                    });

                    // Scroll to bottom after messages are loaded
                    body.scrollTop(body.prop("scrollHeight"));
                },
                error: function(jqXHR, textStatus, errorThrown) {
                    console.error("Failed to fetch messages:", textStatus, errorThrown, jqXHR.responseText);
                    body.empty().append(`<div class="message system">Failed to load messages. Please try again later.</div>`);
                }
            });
        }

        function formatTimestamp(timestamp) {
            if (!timestamp) return '';
            try {
                const date = new Date(timestamp);
                if (isNaN(date.getTime())) {
                    throw new Error('Invalid date object');
                }
                const hours = String(date.getHours()).padStart(2, '0');
                const minutes = String(date.getMinutes()).padStart(2, '0');
                return `${hours}:${minutes}`;
            } catch (e) {
                console.error("Error formatting timestamp:", timestamp, e);
                return 'Invalid time';
            }
        }

        $(document).ready(function() {
            // --- Send message form submission ---
            $('#message-form').on('submit', function(e) {
                e.preventDefault();

                const classId = $("#classID").val();
                const userId = currentUserID;
                const messageContent = $('#message-input').val().trim();

                // --- Validation ---
                if (!messageContent) {
                    return;
                }
                if (!classId) {
                    alert("Error: No class selected.");
                    return;
                }
                if (userId === null) {
                    alert("Error: Cannot send message. User not identified. Please refresh and log in.");
                    return;
                }

                const postUrl = `http://localhost/classes/${classId}/messages/create`;
                const postData = {
                    classID: classId,
                    userID: userId,
                    message: messageContent
                };

                // Provide visual feedback during sending
                const $sendButton = $('#send-button');
                const $messageInput = $('#message-input');
                $sendButton.prop('disabled', true).css('opacity', 0.5);
                $messageInput.prop('disabled', true);

                $.ajax({
                    url: postUrl,
                    method: 'POST',
                    data: postData,
                    dataType: 'json',
                    success: function(response) {
                        // Clear the input field AFTER successful send
                        $('#message-input').val('');

                        // Refresh the message list to include the new message
                        loadMessagesForClass(classId);

                        // Re-enable input/button and focus input
                        $messageInput.prop('disabled', false).focus();
                        $sendButton.prop('disabled', false).css('opacity', 1);

                    },
                    error: function(jqXHR, textStatus, errorThrown) {
                        console.error("Failed to send message:", textStatus, errorThrown, jqXHR.responseText);
                        alert(`Error sending message: ${jqXHR.responseJSON?.error || 'Please try again.'}`); // Show specific error if available

                        // Re-enable input/button even on error
                        $messageInput.prop('disabled', false);
                        $sendButton.prop('disabled', false).css('opacity', 1);
                    }
                });
            });
        });
    </script>

</body>

</html>