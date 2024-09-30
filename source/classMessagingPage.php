<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Education Portal Class Tab</title>
    <link rel="stylesheet" href="stylesheets/partials/sidebar.css">
    <link rel="stylesheet" href="stylesheets/common.css">
    <link rel="stylesheet" href="stylesheets/classMessagingPage/main.css">
    <script>
        0
    </script>
</head>

<!-- Navigation Bar -->
<?php $page = 'classTab';
require 'partials/navBar.php' ?>

<body>
    <!-- Sidebar -->
    <aside id="sidebar" class="sidebar">
        <h2> Classes </h2>
        <menu id="class-menu">
        </menu>
    </aside>

    <!-- Main wrapper -->
    <div id="main-wrapper">
        <div id="classChat-header" class="classChat">
            <div id="left-section">
                <img src="icons/schedule-svgrepo-com.svg">
                <div id="classChat-description"> Subject, Level, Group </div>
            </div>
            <button id="viewMembers-button" class="indigoTheme roundBorder shadow">
                View Members
            </button>
        </div>
        <div id="classChat-body" class="classChat">
        </div>
        <div id="classChat-footer" class="classChat">
            <form id="message-form" method="post" action="ClassMessaging/sendMessage.php">
                <textarea name="message-input" id="message-input" placeholder="Input your message here"></textarea>
                <img id="send-button" class="shadow" src="icons/send.svg">
            </form>
        </div>
        <div id="classChat-cover">
            No Class Selected
        </div>
    </div>
    <?php
    require 'partials/popUp.php';
    ?>
    <script src="scripts/classMessagingPage.js" type="module"></script>
</body>

</html>