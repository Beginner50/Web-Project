<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>

<body>

    <?php

    require_once '../connect.php';
    session_start();

    $_SESSION['UserID-Clicked'] = 3;
    $_SESSION['UserType'] = 'Student';
    $_SESSION['Name'] = 'abc Deez';
    $_SESSION['FirstName'] = 'abc';
    $_SESSION['LastName'] = 'Deez';

    $_SESSION['DateJoined'] = NULL;
    $_SESSION['Authorisation'] = NULL;


    $stmt = $pdo->prepare('SELECT * FROM user WHERE UserID = ?');
    $stmt->execute([$_SESSION['UserID-Clicked']]);
    $User = $stmt->fetch(PDO::FETCH_ASSOC);

    $_SESSION['Gender'] = $User['Gender'];
    $_SESSION['DateOfBirth'] = $User['DateOfBirth'];
    $_SESSION['Email'] = $User['Email'];


    // If user is a student, query the student table using UserID to get additional data.
    if ($_SESSION['UserType'] == 'Student') {

        $stmt = $pdo->prepare("SELECT Level, ClassGroup FROM student WHERE StudentID=?;");
        $stmt->bindParam(1, $_SESSION['UserID-Clicked']);
        $stmt->execute();
        $student = $stmt->fetch(PDO::FETCH_ASSOC);

        $_SESSION['Level'] = $student['Level'];
        $_SESSION['ClassGroup'] = $student['ClassGroup'];

        // Retrieve subjects taken by the student
        $stmt = $pdo->prepare("  SELECT s.Subjectname, s.SubjectCode FROM subject s 
                                INNER JOIN class c ON s.SubjectCode = c.SubjectCode
                                INNER JOIN class_student cs ON cs.ClassId = c.ClassID
                                WHERE cs.StudentID= ?;");

        $stmt->bindParam(1, $_SESSION['UserID-Clicked']);
        $stmt->execute();
        $subjects = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if (!empty($subjects)) {
            $_SESSION['Subjects'] = $subjects;
        } else {
            echo "No subjects found for this student.";
        }
    }
    // Else if user is a teacher
    else if ($_SESSION['UserType'] == 'Teacher') {

        $stmt = $pdo->prepare("SELECT SubjectTaught, DateJoined FROM teacher WHERE TeacherID=?;");
        $stmt->bindParam(1, $_SESSION['UserID-Clicked']);
        $stmt->execute();
        $teacher = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($teacher) {
            $_SESSION['SubjectTaught'] = $teacher['SubjectTaught'];
            $_SESSION['DateJoined'] = $teacher['DateJoined'];
        }
    }

    header("Location: adminPage.php");
    exit();
    ?>
</body>

</html>