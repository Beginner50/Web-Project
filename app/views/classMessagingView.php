<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Education Portal Class Tab</title>
    <link rel="stylesheet" href="stylesheets/common.css">
    <link rel="stylesheet" href="stylesheets/navBar.css">
    <link rel="stylesheet" href="stylesheets/classPage/main.css">
    <script>
        0
    </script>
</head>

<!-- Navigation Bar -->
<?php $page = 'classTab';
require_once('navBar.php') ?>

<body>
    <div id="sidebar">
        <h1> Classes </h1>
        <menu id="class-menu">
        </menu>
    </div>
    <div id="main-wrapper">
        <div id="classChat-header" class="classChat">
            <div id="left-section">
                <img src="icons/schedule-svgrepo-com.svg">
                <div> Subject, Level, Group </div>
            </div>
            <button id="viewMembers-button" class="indigoTheme roundBorder shadow">
                View Members
            </button>
        </div>
        <div id="classChat-body" class="classChat">
        </div>
        <div id="classChat-footer" class="classChat">
            <form id="message-form" method="post" action="">
                <textarea name="message-input" id="message-input" placeholder="Input your message here"></textarea>
                <img id="send-button" class="shadow" src="icons/send.svg">
            </form>
        </div>
    </div>
    <script src="scripts/classTab.js"></script>
</body>

</html>