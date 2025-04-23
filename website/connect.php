<?php
$host = "localhost";
$user = "prashant";
$db = "web_project";
$pass = "";

// Creates a new pdo object
$pdo = new PDO(
    'mysql:host=' . $host . ';' . 'dbname=' . $db,
    $user,
    $pass
);
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
