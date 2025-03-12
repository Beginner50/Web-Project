<?php
ini_set('display_startup_errors', 1);
ini_set('display_errors', 1);
error_reporting(-1);

session_start();
require_once 'connect.php';

// Note: Cookies need to be read instead of session data for this
// If user enters the website after a fresh session, redirect to authentication, otherwise redirect to account-management
if (!isset($_GET['page'])) {
    if (!isset($_SESSION['UserType']))
        header("Location: /authentication");
    else
        header("Location: /account");
    exit;
}

// Routing Logic
switch ($page = $_GET['page']) {
    case "authentication":
        $subjects = include 'models/Authentication/getSubjects.php';
        require 'views/Authentication/authenticationView.php';
        break;
    case "dashboard":
        if (isset($_SESSION['UserType']) && $_SESSION['UserType'] == 'Admin')
            require 'views/AdminDashboard/adminDashboardView.php';
        break;
    case "account":
        if (isset($_SESSION['UserType']))
            require 'views/AccountManagement/accountManagementView.php';
        break;
    case "messaging":
        if (isset($_SESSION['UserType']))
            require 'views/ClassMessaging/classMessagingView.php';
        break;
        // To delete all section below and re-write form submission logic to use AJAX
    case "registration":
    case "login":
        $errors = [];

        // To convert into REST API, put all auth logic into a user handler and make it return a json
        if ($page == "registration")
            $errors = require 'models/Authentication/registration.php';
        else if ($page == "login")
            $errors = require 'models/Authentication/login.php';

        // Display errors (if any), or redirect to account page
        if ($errors)
            require 'views/Authentication/authenticationErrorView.php';
        else
            header('Location: /account');
        break;
};
