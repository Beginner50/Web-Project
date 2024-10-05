<?php

session_start();
require_once '../connect.php';

$subjectCode = $_POST['delete-subjectCode'];
$StudentID = $_SESSION['UserID-Clicked'];

// Log incoming POST data for debugging
error_log(print_r($_POST, true));

$stmt = $pdo->prepare('DELETE cs FROM class_student cs  
                       JOIN class c 
                       ON cs.ClassID = c.ClassID 
                       WHERE c.SubjectCode = ? AND cs.StudentID = ?');

if ($stmt->execute([$subjectCode, $StudentID])) {
    $_SESSION['subjectDeletStatus'] = 'Subject ' . $subjectCode . ' has been removed';

    // Debug: Check current subjects before deletion
    error_log(print_r($_SESSION['Subjects'], true));

    foreach ($_SESSION['Subjects'] as $index => $subject) {
        if ($subject['SubjectCode'] == $subjectCode) {
            unset($_SESSION['Subjects'][$index]);
            $_SESSION['Subjects'] = array_values($_SESSION['Subjects']); // re-index the array
            break;
        }
    }

    // Debug: Check subjects after deletion
    error_log(print_r($_SESSION['Subjects'], true));
} else {
    $_SESSION['subjectDeletStatus'] = 'Error deleting';
}

// Redirect to the admin page
header('Location: adminPage.php');
exit();
?>
