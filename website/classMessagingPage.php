<?php
header("Access-Control-Allow-Origin: *");
session_start();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="expires" content="0">
    <meta http-equiv="Cache-Control" content="no-cache">
    <title>Education Portal Class Tab</title>
    <link rel="stylesheet" href="stylesheets/partials/sidebar.css">
    <link rel="stylesheet" href="stylesheets/common.css">
    <link rel="stylesheet" href="stylesheets/classMessagingPage/main.css">
    <link rel="stylesheet" href="stylesheets/partials/popUp.css">
    <link rel="stylesheet" href="stylesheets/partials/navBar.css">
    <script>
        0
    </script>
</head>

<!-- Navigation Bar -->
<?php $page = 'classTab';
require 'partials/navBar.php';
?>

<body>
    <!-- Sidebar -->
    <aside id="sidebar" class="sidebar">
        <h2> Classes </h2>
        <menu id="class-menu">
            <?php
            include 'ClassMessaging/getClasses.php';
            foreach ($results as $result)
                echo '<ul data-classID="' . $result['ClassID'] . '">'
                    . $result['SubjectName'] . ', LEVEL '
                    . $result['Level'] . ', '
                    . $result['ClassGroup'] .
                    '</ul>';
            ?>
        </menu>
    </aside>

    <!-- Main wrapper -->
    <div id="main-wrapper">
        <!-- Class Chat Header -->
        <div id="classChat-header" class="classChat" style="display: none;">
            <div id="left-section">
                <img src="assets/schedule-svgrepo-com.svg">
                <div id="classChat-description"> Subject, Level, Group </div>
            </div>
            <button id="viewMembers-button" class="indigoTheme roundBorder shadow">
                View Members
            </button>
        </div>
        <!-- Class Chat Body -->
        <div id="classChat-body" class="classChat" style="display: none;">
        </div>
        <!-- Class Chat Footer -->
        <div id="classChat-footer" class="classChat" style="display: none;">
            <form id="message-form" method="post" action="ClassMessaging/sendMessage.php">
                <textarea name="message-input" id="message-input" placeholder="Input your message here"></textarea>
                <button id="send-button" form="none">
                    <img src="assets/send.svg"></img>
                </button>
            </form>
        </div>
        <!-- Class Chat Cover -->
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