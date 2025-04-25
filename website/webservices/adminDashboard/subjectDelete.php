<?php
require_once '../../connect.php'; // Adjusted path

$subjectCode = $_POST['subjectCode'];
$studentID = $_POST['userID']; // Directly from form, not session

$stmt = $pdo->prepare('DELETE cs FROM class_student cs  
                       JOIN class c 
                       ON cs.ClassID = c.ClassID 
                       WHERE c.SubjectCode = ? AND cs.StudentID = ?');

if ($stmt->execute([$subjectCode, $studentID])) {
    header('Location: /dashboard'); // or wherever your admin page is
    exit();
} else {
    echo "Error deleting subject.";
}
?>
