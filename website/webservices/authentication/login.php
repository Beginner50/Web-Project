<?php
session_start();
require_once '../../connect.php';
$errors = [];

$userType;
$email = $_POST["email"];
$password = $_POST["password"];
$userID;
$dateOfBirth;
$firstName;
$lastName;
$gender;

$level;
$classGroup;
$subjects;

$subjectTaught;
$dateJoined;

function getUserRecordFromEmail($pdo, $email)
{
    /* 
    Fetch the record of the user (if it exists) from the database
    */
    $stmt = $pdo->prepare('SELECT * FROM user WHERE Email=?;');
    $stmt->bindParam(1, $email);
    $stmt->execute();

    if (($userRecords =  $stmt->fetchAll(PDO::FETCH_ASSOC)) != null)
        return $userRecords[0];
    return null;
}

function getStudentRecordFromUserID($pdo, $userID)
{
    $stmt = $pdo->prepare('SELECT * FROM student WHERE StudentID=?;');
    $stmt->bindParam(1, $userID);
    $stmt->execute();

    if ($studentRecords =  $stmt->fetchAll(PDO::FETCH_ASSOC))
        return $studentRecords[0];
    return null;
}

function getTeacherRecordFromUserID($pdo, $userID)
{
    $stmt = $pdo->prepare('SELECT * FROM teacher WHERE TeacherID = ?');
    $stmt->bindParam(1, $userID);
    $stmt->execute();

    if ($teacherRecords =  $stmt->fetchAll(PDO::FETCH_ASSOC))
        return $teacherRecords[0];
    return null;
}

function getAdminRecordFromUserID($pdo, $userID)
{
    $stmt = $pdo->prepare("SELECT * FROM administrator WHERE AdminID=?;");
    $stmt->bindParam(1, $userID);
    $stmt->execute();
    if (($adminRecords = $stmt->fetchAll(PDO::FETCH_ASSOC)) != null)
        return $adminRecords[0];
    return null;
}

// If email corresponds to an existing user, store the user data
if (($userFetched = getUserRecordFromEmail($pdo, $email)) != null) {
    // If the passwords match, get user data
    if (password_verify($password, $userFetched["Password"])) {
        $userID = $userFetched['UserID'];
        $dateOfBirth = $userFetched['DateOfBirth'];
        $firstName = $userFetched['FirstName'];
        $lastName = $userFetched['LastName'];
        $gender = $userFetched['Gender'];

        // Get the userType specific data
        if (($studentRecord = getStudentRecordFromUserID($pdo, $userID)) != null) {
            $userType = 'Student';
            $level = $studentRecord['Level'];
            $classGroup = $studentRecord['ClassGroup'];

            // Retrieve subjects taken by the student
            $stmt = $pdo->prepare("  SELECT s.Subjectname, s.SubjectCode FROM subject s 
                                            INNER JOIN class c ON s.SubjectCode = c.SubjectCode
                                            INNER JOIN class_student cs ON cs.ClassId = c.ClassID
                                            WHERE cs.StudentID= ?;");
            $stmt->bindParam(1, $userID);
            $stmt->execute();
            $subjects = $stmt->fetchAll(PDO::FETCH_ASSOC);

            if (empty($subjects)) {
                array_push($errors, 'No subjects selected!');
            } else if (count($subjects) < 5) {
                array_push($errors, 'Less than 5 subjects selected!');
            }
        } else if (($teacherRecord = getTeacherRecordFromUserID($pdo, $userID)) != null) {
            $userType = 'Teacher';
            $subjectTaught = $teacherRecord['SubjectTaught'];
            $dateJoined = $teacherRecord['DateJoined'];
        } else if (($adminRecord = getAdminRecordFromUserID($pdo, $userID)) != null) {
            $userType = 'Admin';
            $dateJoined = $adminRecord['DateJoined'];
        } else
            array_push($errors, 'User has no type!');
    } else
        array_push($errors, 'Passwords do not match!');
} else
    array_push($errors, 'Email does not correspond to any user!');


// Save session if all login information is valid
if (count($errors) == 0) {
    $_SESSION['UserType'] = $userType;
    $_SESSION['UserID'] = $userID;
    $_SESSION['DateOfBirth'] = $dateOfBirth;
    $_SESSION['FirstName'] = $firstName;
    $_SESSION['LastName'] = $lastName;
    $_SESSION['Gender'] = $gender;
    $_SESSION['Email'] = $email;
    $_SESSION['Password'] = $password;

    $_SESSION['Level'] = $level ?? null;
    $_SESSION['ClassGroup'] = $classGroup ?? null;
    $_SESSION['Subjects'] = $subjects ?? null;

    $_SESSION['SubjectTaught'] = $subjectTaught ?? null;
    $_SESSION['DateJoined'] = $dateJoined ?? null;
}

header("Content-Type: application/json");
if (empty($errors))
    header("HTTP/1.1 200 Ok");
else
    header("HTTP/1.1 409 Conflict");
echo json_encode($errors);
