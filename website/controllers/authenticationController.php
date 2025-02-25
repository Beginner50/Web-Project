<?php
// Display the authentication page if no form has been submitted
// Otherwise, execute registration/login model logic for the corresponding form
if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    $page = 'authenticationPage';
    $subjects = include 'models/Authentication/getSubjects.php';
    require 'views/authenticationView.php';
} else
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $formType = substr($_SERVER['REQUEST_URI'], 1);
    $errors = [];

    if ($formType == "registration")
        $errors = require 'models/Authentication/registration.php';
    else if ($formType == "login")
        $errors = require 'models/Authentication/login.php';

    // If there are any errors, display them accordingly
    // Otherwise, redirect to the normal/admin user dashboard controller
    if ($errors) {
        require 'views/partials/Authentication/authenticationErrorView.php';
    } else {
        header('Location: /portal?page=account-management');
        exit;
    }
}
