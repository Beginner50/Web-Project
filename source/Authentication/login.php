<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>

    <link rel="stylesheet" href="../stylesheets/common.css">
    <link rel="stylesheet" href="../stylesheets/authenticationPage/common.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
</head>

<body style="background-color: var(--duskSky);">
    <div class="container">
        <?php
        session_start();
        require_once '../connect.php';

        $email = $_POST["email"];
        $password = $_POST["password"];

        // Get user data from email if user exists
        $stmt = $pdo->prepare('SELECT * FROM user WHERE Email=?;');
        $stmt->bindParam(1, $email);
        $stmt->execute();
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        // Get the user type
        // Check if user is a student
        $stmt = $pdo->prepare('SELECT * FROM student WHERE StudentID=?;');
        $stmt->bindParam(1, $user['UserID']);
        $stmt->execute();
        if ($stmt->rowCount() > 0)
            $_SESSION['UserType'] = 'Student';

        // Check if user is a teacher or admin only if user is not a student
        if ($_SESSION['UserType'] != 'Student') {
            $stmt = $pdo->prepare('SELECT * FROM teacher WHERE TeacherID=?;');
            $stmt->bindParam(1, $user['UserID']);
            $stmt->execute();
            if ($stmt->rowCount() > 0)
                $_SESSION['UserType'] = 'Teacher';
            else
                $_SESSION['UserType'] = 'Admin';
        }

        if ($user) {
            if (password_verify($password, $user["Password"])) {
                $_SESSION['Email'] = $user['Email'];
                $_SESSION['Password'] = $user['Password'];

                $_SESSION['UserID'] = $user['UserID'];
                $_SESSION['DateOfBirth'] = $user['DateOfBirth'];
                $_SESSION['FirstName'] = $user['FirstName'];
                $_SESSION['LastName'] = $user['LastName'];
                $_SESSION['Gender'] = $user['Gender'];

                // If user is a student, query the student table using UserID to get additional data.
                if ($_SESSION['UserType'] == 'Student') {
                    $stmt = $pdo->prepare("SELECT Level, ClassGroup FROM student WHERE StudentID=?;");
                    $stmt->bindParam(1, $user['UserID']);
                    $stmt->execute();
                    $student = $stmt->fetch(PDO::FETCH_ASSOC);

                    $_SESSION['Level'] = $student['Level'];
                    $_SESSION['ClassGroup'] = $student['ClassGroup'];

                    // Retrieve subjects taken by the student
                    $stmt = $pdo->prepare("  SELECT s.SubjectName,cs.SubjectCode
                                    FROM class_student cs
                                    INNER JOIN subject s ON cs.SubjectCode = s.SubjectCode
                                    WHERE cs.StudentID=?;  
                                ");
                    $stmt->bindParam(1, $user['UserID']);
                    $stmt->execute();
                    if ($stmt->rowCount() > 0)
                        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                            echo "Subject Name: " . $row['SubjectName'] . "<br>";
                        }
                    else
                        echo "No subjects found for this student.";
                }
                // Else if user is a teacher
                else if ($_SESSION['UserType'] == 'Teacher') {
                    $stmt = $pdo->prepare("SELECT SubjectTaught, DateJoined FROM teacher WHERE TeacherID=?;");
                    $stmt->bindParam(1, $user['UserID']);
                    $stmt->execute();
                    $teacher = $stmt->fetch(PDO::FETCH_ASSOC);

                    if ($teacher) {
                        $_SESSION['SubjectTaught'] = $teacher['SubjectTaught'];
                        $_SESSION['DateJoined'] = $teacher['DateJoined'];
                    }
                }
                // Else if user is an admin
                else if ($_SESSION['UserType'] == 'Admin') {
                }

                // Redirect to accountManagementPage
                header("Location: ../accManagementPage.php");
                exit();
            } else {
                echo "<h2 style='text-align: center; color: rgb(53, 12, 12);  '>Login Unsuccessfull </h2>";
                echo "<div class='alert alert-danger'>Password does NOT match!</div>";
                echo "<a href='javascript:self.history.back()'><button class='indigoTheme roundBorder' style=' margin-top: 15px; border-width: 4px; font-size:25px; padding:0px 15px;'> Back </button>";
            }
        } else {
            echo "<h2 style='text-align: center; color: rgb(53, 12, 12);  '>Login Unsuccessfull </h2>";
            echo "<div class='alert alert-danger'>Email does NOT exist!</div>";
            echo "<a href='javascript:self.history.back()'><button class='indigoTheme roundBorder' style=' margin-top: 15px; border-width: 4px; font-size:25px; padding:0px 15px;'> Back </button>";
        }
        ?>
    </div>
</body>

</html>