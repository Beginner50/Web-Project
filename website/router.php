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
        require 'views/authentication/authenticationView.php';
        break;
    case "dashboard":
        if (isset($_SESSION['UserType']) && $_SESSION['UserType'] == 'Admin') {
            require 'models/adminDashboard/getListUsers.php';
            require 'views/adminDashboard/adminDashboardView.php';
        }
        break;
    case "account":
        if (isset($_SESSION['UserType']))
            require 'views/accountManagement/accountManagementView.php';
        break;
    case "messaging":
        if (isset($_SESSION['UserType']))
            require 'views/classMessaging/classMessagingView.php';
        break;
};
