<?php

require_once '../connect.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $stmt = $pdo->prepare('SELECT Message FROM class_message INNER JOIN class ON class_message.ClassID = class.ClassID WHERE class.ClassID=?;');
    $stmt->bindParam(1, $_POST['ClassID']);

    $stmt->execute();
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($results);
}
