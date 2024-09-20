<?php

$host = "localhost";
$user = "root";
$db="web_project";
$pass = "";
// $pass="umair1108";

$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
    echo "Failed to connect to database" . $conn->connect_error;
}
