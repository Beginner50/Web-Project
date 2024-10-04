<?php

ini_set('display_startup_errors', 1);
ini_set('display_errors', 1);
error_reporting(-1);
$host = "localhost";
$user = "root";
$db = "web_project";
// $pass = "";
$pass = " ";
// $pass="umair1108";
$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
    echo "Failed to connect to database" . $conn->connect_error;
}

$pdo;
// Creates a new pdo object
try {
    $pdo = new PDO(
        'mysql:host=' . $host . ';' . 'dbname=' . $db,
        $user,
        $pass
    );
} catch (PDOException $e) {
    die($e);
}
