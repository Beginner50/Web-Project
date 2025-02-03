<?php
ini_set('display_startup_errors', 1);
ini_set('display_errors', 1);
error_reporting(-1);

session_start();
// Require once instead of require since pdo instantiated only once
require_once 'connect.php';

if (
    $_SERVER['REQUEST_URI'] == '/'
    || $_SERVER['REQUEST_URI'] == '/login'
    || $_SERVER['REQUEST_URI'] == '/registration'
) {
    if (isset($_SESSION['UserType']))
        header("Location: /dashboard");
    require 'controllers/authenticationController.php';
} else if ($_SERVER['REQUEST_URI'] == '/dashboard' && isset($_SESSION['UserType'])) {
    if ($_SESSION['UserType'] === 'Admin')
        require 'controllers/adminDashboardController.php';
    else
        require 'controllers/userDashboardController.php';
} else {
    header("Location: /");
    exit;
}
