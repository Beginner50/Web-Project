<?php
if (!isset($queryParams['page']))
    header('Location: /portal?page=account-management');

if ($queryParams['page'] == 'account-management') {
    $page = 'accountManagementPage';
    require 'views/accountManagementView.php';
} else if ($queryParams['page'] == 'class-messaging' && $_SESSION['UserType'] != 'Admin') {
    $page = 'classMessagingPage';
    require 'views/classMessagingView.php';
} else if ($queryParams['page'] == 'admin-dashboard' && $_SESSION['UserType'] == 'Admin') {
    $page = 'adminDashboardPage';
    require 'views/adminDashboardView.php';
} else {
    echo 'Error: Trying to access a page with invalid permissions';
    die;
}
