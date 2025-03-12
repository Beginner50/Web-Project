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
    <!-- FOR SUBJECT LIST & MODAL ONLY -->
    <!-- Placeholders for callbacks (Similar to react hooks) -->
    <script>
        window.sharedState = {
            onSubjectSelect: null, // Callback for modal to react when subject is deselected from subjectList.
            onSubjectDeselect: null, // Callback for subjectList to react when subject is selected from modal.
            selectedSubjects: 0
        }
    </script>

    <!-- Navigation Bar -->
    <?php require 'views/partials/navBar.php'; ?>

    <!-- Main Wrapper -->
    <div id="main-wrapper" class="roundBorder-15">
        <!-- Call To Action Wrapper (Switches between Login & Registration)-->
        <div id="callToAction-wrapper" class="registrationCTA">
            <?php require 'views/Authentication/callToAction.php' ?>
        </div>

        <!-- Form Wrapper -->
        <div id="form-wrapper">
            <h1>Sign In</h1>

            <!-- Login Form (Default: Visible) -->
            <?php require 'views/Authentication/loginForm.php' ?>

            <!-- Registration Form (Default: Hidden)-->
            <?php require 'views/Authentication/registrationForm.php' ?>
        </div>
    </div>

    <!-- Subject Modal (Default: Hidden) -->
    <?php require 'views/Authentication/subjectModal.php'; ?>
</body>

</html>