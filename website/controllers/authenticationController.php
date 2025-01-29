<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Authentication Page for the school website of ABC academy">
    <base href="/website/">
    <link rel="stylesheet" href="stylesheets/common.css">
    <link rel="stylesheet" href="stylesheets/authenticationPage/main.css">
    <link rel="stylesheet" href="stylesheets/authenticationPage/registrationFormGeneral.css">
    <link rel="stylesheet" href="stylesheets/authenticationPage/registrationFormSpecific.css">
    <title>School Website</title>
</head>

<body>
    <?php
    $page = 'authenticationPage';
    $subjects = include 'Authentication/getSubjects.php';

    // Navigation Bar
    require 'views/partials/navBar.php';

    // Authentication View
    require 'views/authenticationView.php';

    // Subject Modal (Default: Hidden)
    require 'views/partials/modal.php';
    ?>
</body>

</html>