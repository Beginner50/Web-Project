<?php
ini_set('display_startup_errors', 1);
ini_set('display_errors', 1);
error_reporting(-1);


if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username =  filter_var(htmlspecialchars($_POST['email']), FILTER_VALIDATE_EMAIL);
    $password =  htmlspecialchars($_POST['password']);

    // Authentication code here
    $auth = true;
    if (!$auth) {
        return;
    }

    // From the data obtained during authentication, redirect the user to new website
    header('Location: ../classTab.php');
}
die();
