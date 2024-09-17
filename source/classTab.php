<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Education Portal Class Tab</title>
    <link rel="stylesheet" href="stylesheets/common.css">
    <link rel="stylesheet" href="stylesheets/classPage/common.css">
</head>
    <!-- Navigation Bar -->
    <?php $page='classTab'; require_once('navBar.php') ?>
<body>
</body>
    <div id="main-wrapper">
    <div id="left-wrapper">
        <h1> Classes </h1>
        <div id="class-menu"></div>
    </div>
    <div id="right-wrapper">
        <div id="classChat-header">
            <div id="left-section"> 
                <div> Logo </div>
                <div> Subject, Level, Group </div>
            </div>
            <button id="viewMembers-button" class="indigoTheme roundBorder shadow">
                 View Members 
            </button>
        </div>
        <div id="classChat-body"></div>
        <div id="classChat-footer">
            <form id="message-form" method="post" action="phpFunctions/sendMessage.php">
                    <input name="message-input" id="message-input" placeholder="Input your message here">
                    <button type="submit" form="message-form"> Submit </button>
            </form>
        </div>
    </div>
    </div>
</html>