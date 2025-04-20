<?php
ini_set('display_startup_errors', 1);
ini_set('display_errors', 1);
error_reporting(-1);

session_start();
require_once 'connect.php';

function sendPostRequest($url, $data)
{
    $postData = http_build_query($data);

    $context = stream_context_create([
        'http' => [
            'method'  => 'POST',
            'header'  => implode("\r\n", [
                'Content-Type: application/x-www-form-urlencoded',
                'Content-Length: ' . strlen($postData),
                'Accept: application/json'
            ]),
            'content' => $postData,
            'ignore_errors' => true
        ]
    ]);

    $response = file_get_contents($url, false, $context);
    return $response;
}

function redirectAuthenticationOrRestoreSession()
{
    if (!isset($_SESSION['userType']))
        header("Location: /authentication");
    else
        header("Location: /account/" . $_SESSION["userType"] . "/" . $_SESSION["userID"]);
    exit;
}


// Routing Logic
if (!isset($_GET['page']))
    redirectAuthenticationOrRestoreSession();

switch ($page = $_GET['page']) {
    case "authentication":
        $errors = [];
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            if ($_GET["action"] == "login")
                $url = "http://localhost/users/authenticate";
            else if ($_GET["action"] == "registration")
                $url = "http://localhost/users/" . $_POST["user-type"] . "/create";

            $response = json_decode(sendPostRequest($url, $_POST), true);
            if ($response["success"]) {
                $_SESSION["userType"] = strtolower($response["data"]["userType"]);
                $_SESSION["userID"] = $response["data"]["userID"];

                header("Location: /account/" . $_SESSION["userType"] . "/" . $_SESSION["userID"]);
                exit;
            } else {
                $errors = $response["errors"];
                require "views/partials/errorModal.php";
            }
        }

        require 'views/authentication/authenticationView.php';
        break;
    case "dashboard":
        if (isset($_SESSION['userType']) && $_SESSION['userType'] == 'Admin') {
            require 'models/adminDashboard/getListUsers.php';
            require 'views/adminDashboard/adminDashboardView.php';
        }
        break;
    case "account":
        if (isset($_SESSION['userType'])) {
            $userData = json_decode(
                file_get_contents("http://localhost/users/" . $_GET["user-type"] . "/" . $_GET["userID"]),
                true
            )["data"][0];

            require 'views/accountManagement/accountManagementView.php';
        }
        break;
    case "messaging":
        if (isset($_SESSION['userType']))
            require 'views/classMessaging/classMessagingView.php';
        break;
    case "logout":
        unset($_SESSION["userType"]);
        unset($_SESSION["userID"]);
        header("Location: /");
        break;
};
