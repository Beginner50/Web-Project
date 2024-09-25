<?php

require_once '../connect.php';
ini_set('display_errors', 1);

// To change ul into buttons and assign them values of classID
// Modify response so that only classID needs to be sent
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $stmt = $pdo->prepare('SELECT Message FROM class_message INNER JOIN class ON class_message.ClassID = class.ClassID WHERE SubjectCode=? AND Level=? AND ClassGroup=?;');
    $stmt->bindParam(1, $_POST['SubjectCode']);
    $stmt->bindParam(2, $_POST['Level']);
    $stmt->bindParam(3, $_POST['ClassGroup']);

    $stmt->execute();
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($results);
}
