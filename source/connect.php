<?php

ini_set('display_startup_errors', 1);
ini_set('display_errors', 1);
error_reporting(-1);
$host = "localhost";
$user = "root";
$db = "web_project";
$pass = "";

$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) {
    echo "Failed to connect to database" . $conn->connect_error;
}

// Creates a new pdo object
$pdo = new PDO(
    'mysql:host=' . $host . ';' . 'dbname=' . $db,
    $user,
    $pass
);
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
