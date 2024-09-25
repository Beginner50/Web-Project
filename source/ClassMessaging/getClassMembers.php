<?php
require_once '../connect.php';

$classID = 1;

$stmt = $pdo->prepare("SELECT FirstName, LastName FROM class_student INNER JOIN user ON class_student.StudentID = user.UserID WHERE ClassID = ?");
$stmt->bindParam(1, $classID, PDO::PARAM_INT);

$stmt->execute();
$results = $stmt->fetchAll(PDO::FETCH_ASSOC);
echo json_encode($results);
