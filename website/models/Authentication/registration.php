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

function pushErrorIfEmpty($variable, $errorsArray, $errorMessage)
{
    if (empty($variable) || $variable == "")
        array_push($errorsArray, $errorMessage);
}

$userType = htmlspecialchars($_POST["user-type"]);
pushErrorIfEmpty($userType, $errors, "User Type is Empty!");

// Get and sanitize all general attributes from POST request
$firstName = htmlspecialchars($_POST["fname"]);
pushErrorIfEmpty($firstName, $errors, "First Name is Empty!");
$lastName = htmlspecialchars($_POST["lname"]);
pushErrorIfEmpty($lastName, $errors, "Last Name is Empty!");
$email = htmlspecialchars($_POST["email"]);
pushErrorIfEmpty($email, $errors, "Email is Empty!");
$gender = htmlspecialchars($_POST["gender"]);
pushErrorIfEmpty($gender, $errors, " Gender is Empty!");
$dateOfBirth = htmlspecialchars($_POST["dob"]);
pushErrorIfEmpty($dateOfBirth, $errors, "Date of Birth is Empty!");
$password = htmlspecialchars($_POST["password"]);
pushErrorIfEmpty($password, $errors, "Password is Empty!");
$repeatpassword = htmlspecialchars($_POST["repassword"]);
pushErrorIfEmpty($repeatpassword, $errors, "Repeat Password is Empty!");

// Validation of general attributes
if (!filter_var($email, FILTER_VALIDATE_EMAIL))
    array_push($errors, "Email is not valid!");
else {
    // Check if email already exists
    $sTestEmail = $pdo->prepare('SELECT * FROM user WHERE Email = "' . $email . '";');
    $sTestEmail->execute();
    if ($sTestEmail->rowCount() != 0)
        array_push($errors, "Email already exists!");

    // Close the buffer so that I don't get an exception when executing other statements
    $sTestEmail->closeCursor();
}

// Equivalent one-line regex: /^(?=.*[A-Z])(?=.*\d).{5,}$/
if ($password !== $repeatpassword)
    array_push($errors, "Passwords do not match!");
else if (strlen($password) < 5)
    array_push($errors, "Password length must be minimum 5 characters long!");
else if (!preg_match('/[A-Z]/', $password))
    array_push($errors, "Password should contain at least 1 Uppercase!");
else if (!preg_match('/[0-9]/', $password))
    array_push($errors, "Password should contain at least 1 Number!");
$passwordhash = password_hash($password, PASSWORD_DEFAULT); // Hashing of password

// Get, sanitize & validate all specific attributes from POST request
switch ($userType) {
    case "Student":
        $classGroup = htmlspecialchars(strtoupper($_POST["classGroup"]));
        $level = htmlspecialchars($_POST["level"]);
        $subjects = json_decode($_POST["subjects"], true);

        pushErrorIfEmpty($classGroup, $errors, "Class-group cannot be blank!");
        pushErrorIfEmpty($level, $errors, "Level cannot be blank!");
        pushErrorIfEmpty($subjects, $errors, "No subjects selected!");

        if (!filter_var($level, FILTER_VALIDATE_INT)) array_push($errors, "Level is not numeric!");
        if (count($subjects) < 5) array_push($errors, "Select 5 subjects!");
        break;
    case "Teacher":
        $subjectTaught = htmlspecialchars($_POST["subjectTaught"]);
        $dateJoined = htmlspecialchars($_POST["teacherDateJoined"]);

        pushErrorIfEmpty($subjectTaught, $errors, "Subject taught cannot be blank!");
        pushErrorIfEmpty($dateJoined, $errors, "Date Joined cannot be empty!");
        break;
    case "Admin":
        $dateJoined = htmlspecialchars($_POST["adminDateJoined"]);

        pushErrorIfEmpty($dateJoined, $errors, "Date Joined cannot be blank!");
        break;
}

// If there were no errors with input, insert into database
if (count($errors) == 0) {
    switch ($userType) {
        case 'Student':
            // Starts a transaction since I can't pass the subjects array in a stored procedure
            $pdo->beginTransaction();
            try {
                $sInsertStudent = $pdo->prepare('CALL sp_addStudent(?, ?, ?, ?, ?, ?, ?, ?);');
                $sInsertStudent->execute([$firstName, $lastName, $email, $gender, $dateOfBirth, $passwordhash, $classGroup, $level]);
                $studentID = $sInsertStudent->fetchAll(PDO::FETCH_NUM)[0];
                $sInsertStudent->closeCursor();

                $sGetClassID = $pdo->prepare('SELECT ClassID FROM class WHERE SubjectCode = ? AND Level = ? AND ClassGroup = ?;');
                $sAssignClass = $pdo->prepare('INSERT INTO class_student(ClassID, StudentID) VALUES(?, ?);');

                foreach ($subjects as $subject) {
                    $sGetClassID->execute([$subject, $level, $classGroup]);
                    $classID = $sGetClassID->fetchAll(PDO::FETCH_NUM)[0];
                    $sGetClassID->closeCursor();
                    $sAssignClass->execute([$classID[0], $studentID[0]]);
                    $sAssignClass->closeCursor();
                }

                $pdo->commit();
            } catch (Exception $e) {
                var_dump($e);
                if ($pdo->inTransaction())
                    $pdo->rollBack();
                die();
            }
            break;
        case 'Teacher':
            $sInsertTeacher = $pdo->prepare('CALL sp_addTeacher(?, ?, ?, ?, ?, ?, ?, ?)');
            $sInsertTeacher->execute([$firstName, $lastName, $email, $gender, $dateOfBirth, $passwordhash, $subjectTaught, $datejoinedteacher]);
            break;
        case 'Admin':
            $sInsertAdmin = $pdo->prepare('CALL sp_addAdmin(?, ?, ?, ?, ?, ?, ?)');
            $sInsertAdmin->execute([$firstName, $lastName, $email, $gender, $dateOfBirth, $passwordhash, $datejoinedadmin]);
            break;
    }

    // Get userID of user
    $sGetUserID = $pdo->prepare('SELECT UserID FROM user WHERE Email = ?;');
    $sGetUserID->execute([$email]);
    $userID = $sGetUserID->fetchAll(PDO::FETCH_NUM)[0][0];
}
// if (count($errors) == 0) {
//     //displaying sucessful registraton status
//     echo "<h2 style='text-align: center; color: rgb(11, 91, 32); ;  '>Successfully registered!</h2>";
//     echo "<a href='javascript:self.history.back()'><button class='indigoTheme roundBorder' style=' margin-top: 15px; border-width: 2px; font-size:25px;'> Click to here Sign in! </button>";
// }

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

if (empty($errors))
    header("HTTP/1.1 200 Ok");
else
    header("HTTP/1.1 409 Conflict");
header("Content-Type: application/json");
echo json_encode($errors);
