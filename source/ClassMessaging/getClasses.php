<?php
/* Require once directive to ensure that connect.php is not executed again.

   The benefit here is that after running once, the database connection is already
   stored in the server. 

   Subsequent inclusion of the connect.php file by other php files will 
   not cause it to execute again. Thus, the database connection is established
   only once, meaning bigger performance gain, meaning more money saved
*/
require_once 'connect.php';

/*
    Again, the states of global variables are already stored in the php server 
    and are preserved throughout further requests of the php file.
    
    Here, since the classes list will not change for the forseeable future, it
    is not necessary to fetch data from the database again.

    For data that is to change, only cache the prepared statement but execute it
    when the php file is requested again.
*/

// Get the classes associated with the student/teacher
ini_set('display_startup_errors', 1);
ini_set('display_errors', 1);
error_reporting(-1);

$userID = $_SESSION['UserID'];
$default = 0;

// Use views as they are pre-compiled and faster to execute than the corresponding sql statements
$stmt = $pdo->prepare('SELECT class.ClassID AS ClassID, SubjectName, Level, ClassGroup FROM class LEFT JOIN class_student ON class_student.ClassID = class.ClassID INNER JOIN subject ON class.SubjectCode = subject.SubjectCode WHERE StudentID=? OR TeacherID=?;');
if ($_SESSION['UserType'] == 'Student') {
    $stmt->bindParam(1, $userID);
    $stmt->bindParam(2, $default);
} else if ($_SESSION['UserType'] == 'Teacher') {
    $stmt->bindParam(1, $default);
    $stmt->bindParam(2, $userID);
}
$stmt->execute();

$results = $stmt->fetchAll(PDO::FETCH_ASSOC);
