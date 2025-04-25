<?php
$host = "localhost";
$user = "umair";
$db = "web_project";
$pass = "umair1108";

// Creates a new pdo object
$pdo = new PDO(
    'mysql:host=' . $host . ';' . 'dbname=' . $db,
    $user,
    $pass
);
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
