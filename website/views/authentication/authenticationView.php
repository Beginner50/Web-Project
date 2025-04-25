<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Authentication Page for the school website of ABC academy">
    <base href="/website/">
    <link rel="stylesheet" href="stylesheets/common.css">
    <link rel="stylesheet" href="stylesheets/authentication/main.css">
    <link rel="stylesheet" href="stylesheets/authentication/registrationFormGeneral.css">
    <link rel="stylesheet" href="stylesheets/authentication/registrationFormSpecific.css">
    <script src="https://code.jquery.com/jquery-3.7.1.min.js" integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
    <!-- <script src="https://cdn.tailwindcss.com"></script> -->
    <title>School Website</title>
</head>

<body>
    <!-- Navigation Bar -->
    <?php require 'views/partials/navBar.php'; ?>

    <div class="card-snap-wrapper">
        <?php require 'views/authentication/cardContainer.php' ?>

        <div id="main-wrapper" class="roundBorder-15">
            <div id="callToAction-wrapper" class="registrationCTA">
                <!-- Call To Action (Switches between Login & Registration Forms)-->
                <?php require 'views/authentication/CTAComponent.php' ?>
            </div>

            <div id="form-wrapper">
                <h1>Sign In</h1>

                <!-- Login Form (Default: Visible) -->
                <?php require 'views/authentication/loginForm.php' ?>

                <!-- Registration Form (Default: Hidden)-->
                <?php require 'views/authentication/registrationForm.php' ?>
            </div>
        </div>
    </div>
    <?php require "views/authentication/subjectModal.php" ?>
</body>

</html>