<?php
session_start();
require_once '../../connect.php';

header('Content-Type: application/json');

if (!isset($_GET['userID']) || !isset($_GET['userType'])) {
    echo json_encode(['error' => 'Missing userID or userType']);
    exit;
}

$userID = $_GET['userID'];
$userType = $_GET['userType'];

$response = [
    'UserID' => $userID,
    'UserType' => $userType
];

// Common user data
$stmt = $pdo->prepare('SELECT * FROM user WHERE UserID = ?');
$stmt->execute([$userID]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    echo json_encode(['error' => 'User not found']);
    exit;
}

$response['FirstName'] = $user['FirstName'];
$response['LastName'] = $user['LastName'];
$response['Email'] = $user['Email'];
$response['Gender'] = $user['Gender'];
$response['DateOfBirth'] = $user['DateOfBirth'];

if ($userType == 'Student') {
    $stmt = $pdo->prepare("SELECT Level, ClassGroup FROM student WHERE StudentID=?");
    $stmt->execute([$userID]);
    $student = $stmt->fetch(PDO::FETCH_ASSOC);
    $response['Level'] = $student['Level'] ?? '';
    $response['ClassGroup'] = $student['ClassGroup'] ?? '';

    $stmt = $pdo->prepare("SELECT s.Subjectname, s.SubjectCode FROM subject s 
        JOIN class c ON s.SubjectCode = c.SubjectCode 
        JOIN class_student cs ON cs.ClassId = c.ClassID 
        WHERE cs.StudentID=?");
    $stmt->execute([$userID]);
    $response['Subjects'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
} elseif ($userType == 'Teacher') {
    $stmt = $pdo->prepare("SELECT SubjectTaught, DateJoined FROM teacher WHERE TeacherID=?");
    $stmt->execute([$userID]);
    $teacher = $stmt->fetch(PDO::FETCH_ASSOC);
    $response['SubjectTaught'] = $teacher['SubjectTaught'] ?? '';
    $response['DateJoined'] = $teacher['DateJoined'] ?? '';
}

echo json_encode($response);
exit;
