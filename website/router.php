<?php
ini_set('display_startup_errors', 1);
ini_set('display_errors', 1);
error_reporting(-1);

session_start();
// Require once instead of require since pdo instantiated only once
require_once 'connect.php';

$requestURI = parse_url($_SERVER['REQUEST_URI']);
if (isset($requestURI['query']))
    parse_str($requestURI['query'], $queryParams);

switch ($requestURI['path']) {
    case '/':
    case '/login':
    case '/registration':
        // If user has already logged in for this session, redirect user to portal
        if (isset($_SESSION['UserType']))
            header("Location: /portal?page=account-management");
        else
            require 'controllers/authenticationController.php';
        break;
    case '/portal':
        require 'controllers/portalController.php';
        break;
    default:
        header("Location: /");
        exit;
}
