<?php

$host = "localhost";
$user = "root";
// $pass="umair1108";
// $db="web_project";
$pass = " ";
$db = "webProject";

$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
    echo "Failed to connect to database" . $conn->connect_error;
}
