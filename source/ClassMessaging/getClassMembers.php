<?php
session_start();
require_once '../connect.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $classID = $_POST['ClassID'];
    $stmt = $pdo->prepare("SELECT FirstName, LastName, 'Teacher' AS 'UserType' 
                        FROM class INNER JOIN user ON class.TeacherID = user.UserID
                        WHERE class.ClassID=?
                        UNION ALL
                        SELECT FirstName, LastName, 'Student' AS 'UserType' 
                        FROM class_student INNER JOIN user ON class_student.StudentID = user.UserID
                        WHERE ClassID = ?
                       ");
    $stmt->bindParam(1, $classID, PDO::PARAM_INT);
    $stmt->bindParam(2, $classID, PDO::PARAM_INT);

    $stmt->execute();
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($results);
}
