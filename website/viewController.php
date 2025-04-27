<?php
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
    if (!isset($_SESSION['UserType']))
        header("Location: /authentication");
    else
        header("Location: /account/" . strtolower($_SESSION["UserType"]) . "/" . $_SESSION["UserID"]);
    exit;
}


// Routing Logic
if (!isset($_GET['page']))
    redirectAuthenticationOrRestoreSession();

switch ($page = $_GET['page']) {
    case "authentication":
        $errors = [];
        // POST method is when either the login/registration form is submitted
        // Register & Login the user accordingly
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            if ($_GET["action"] == "login")
                $url = "http://localhost/users/authenticate";
            else if ($_GET["action"] == "registration")
                $url = "http://localhost/users/create";
            $response = json_decode(sendPostRequest($url, $_POST), true);

            // If validation/registration is successful, save session data & redirect to account management
            // Otherwise, display errors
            if ($response["success"]) {
                $_SESSION = json_decode(
                    file_get_contents("http://localhost/users/" . strtolower($response["data"]["UserType"]) . "/" . $response["data"]["UserID"]),
                    true
                )["data"][0];

                if ($_SESSION["IsApproved"] != 0) {
                    header("Location: /account/" . strtolower($_SESSION["UserType"]) . "/" . $_SESSION["UserID"]);
                    exit;
                }
            } else
                require "views/partials/errorModal.php";
        }

        require 'views/authentication/authenticationView.php';
        break;
    case "dashboard":
        if (isset($_SESSION['UserType']) && $_SESSION['UserType'] == 'Admin') {
            // json consumption at php level
            $users = json_decode(
                file_get_contents("http://localhost/users?limit=100"),
                true
            )["data"];

            require 'views/adminDashboard/adminDashboardView.php';
        } else {
            header("Location: /");
            exit;
        }
        break;

    case "account":
        if (isset($_SESSION['UserType'])) {
            // If POST request (Change user information)
            if ($_SERVER["REQUEST_METHOD"] === "POST") {
                $_POST["self-userID"] = $_POST["userID"];
                $_POST["self-user-type"] = $_POST["user-type"];

                // Edit the user using form data
                if ($_GET["action"] == "edit-user") {
                    $response = sendPostRequest("http://localhost/users/edit", $_POST);
                }

                // Reload session with new data
                $_SESSION = json_decode(
                    file_get_contents("http://localhost/users/" . strtolower($_SESSION["UserType"]) . "/" . $_SESSION["UserID"]),
                    true
                )["data"][0];
            }
            require 'views/accountManagement/accountManagementView.php';
        } else
            header("Location: /");
        break;
    case "messaging":
        if (isset($_SESSION['UserType'])) {
            $classes = json_decode(
                file_get_contents("http://localhost/users/"
                    . strtolower($_SESSION["UserType"]) . "/" . $_SESSION["UserID"] . "/classes"),
                true
            )["data"];

            require 'views/classMessaging/classMessagingView.php';
        } else
            header("Location: /");
        break;
    case "logout":
        foreach (array_keys($_SESSION) as $key) {
            unset($_SESSION[$key]);
        }
        session_destroy();
        header("Location: /");
        break;
};
