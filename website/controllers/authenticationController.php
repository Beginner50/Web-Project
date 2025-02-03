<?php

// Execute registration/login model logic on corresponding form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $formType = $_POST['form_type'];

    if ($formType == "registration")
        require 'models/Authentication/registration.php';
    else if ($formType == "login")
        require 'models/Authentication/login.php';
}
// Otherwise, display the authentication page
else {
    $page = 'authenticationPage';
    $subjects = include 'models/Authentication/getSubjects.php';
    require 'views/authenticationView.php';
}
