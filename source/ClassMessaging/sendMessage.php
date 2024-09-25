<?php

require_once '../connect.php';
ini_set('display_startup_errors', 1);
ini_set('display_errors', 1);
error_reporting(-1);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $classID = 1;
    $userID = 2;

    $stmt = $pdo->prepare("INSERT INTO class_message(ClassID, UserID, DateSent, Message)
                        VALUES(?,?,'2001/02/15 00:00:01', ?);");
    $stmt->bindParam(1, $classID);
    $stmt->bindParam(2, $userID);
    $stmt->bindParam(3, $_POST['message-input']);

    $stmt->execute();
}
