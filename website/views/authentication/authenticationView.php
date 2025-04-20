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
    <title>School Website</title>
</head>

<body>
    <!-- Navigation Bar -->
    <?php require 'views/partials/navBar.php'; ?>

    <!-- Main Wrapper -->
    <div id="main-wrapper" class="roundBorder-15">
        <!-- Call To Action Wrapper (Switches between Login & Registration Forms)-->
        <div id="callToAction-wrapper" class="registrationCTA">
            <?php require 'views/authentication/CTAComponent.php' ?>
        </div>

        <!-- Form Wrapper -->
        <div id="form-wrapper">
            <h1>Sign In</h1>

            <!-- Login Form (Default: Visible) -->
            <?php require 'views/authentication/loginForm.php' ?>

            <!-- Registration Form (Default: Hidden)-->
            <?php require 'views/authentication/registrationForm.php' ?>
        </div>
    </div>
</body>

</html>