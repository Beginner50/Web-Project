<?php

session_start();
require_once '../connect.php';
ini_set('display_startup_errors', 1);
ini_set('display_errors', 1);
error_reporting(-1);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $stmt = $pdo->prepare("INSERT INTO class_message(ClassID, UserID, DateSent, Message)
                        VALUES(?,?,'2001/02/15 00:00:01', ?);");
    $stmt->bindParam(1, $_POST['ClassID']);
    $stmt->bindParam(2, $_SESSION['UserID']);
    $stmt->bindParam(3, $_POST['message-input']);

    $stmt->execute();
}
