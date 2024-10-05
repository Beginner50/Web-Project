<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>

    <link rel="stylesheet" href="../stylesheets/common.css">
    <link rel="stylesheet" href="../stylesheets/authenticationPage/main.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
</head>

<body style="background-color: var(--duskSky);">
    <div class="container">
        <?php
        session_start();
        require_once '../connect.php';

        $email = $_POST["email"];
        $password = $_POST["password"];

        // Authentication should be carried out before the rest of the code
        // Get user data from email if user exists
        $stmt = $pdo->prepare('SELECT * FROM user WHERE Email=?;');
        $stmt->bindParam(1, $email);
        $stmt->execute();
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        // Seperate this part into a function that finds the type of user
        //get usertype
        $stmt = $pdo->prepare('SELECT * FROM student WHERE StudentID=?;');
        $stmt->bindParam(1, $user['UserID']);
        $stmt->execute();

        $student = $stmt->fetch(PDO::FETCH_ASSOC);
        // Check if a row is returned
        if ($student)
            $_SESSION['UserType'] = 'Student';

        // Check if user is a teacher or admin only if the user is not a student
        if ($_SESSION['UserType'] != 'Student') {
            // Prepare the statement to check if the user is a teacher
            $stmt = $pdo->prepare('SELECT * FROM teacher WHERE TeacherID = ?');
            $stmt->bindParam(1, $user['UserID'], PDO::PARAM_INT); // Bind as an integer
            $stmt->execute();

            // Use fetch() instead of rowCount() for reliability
            $teacher = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($teacher)
                // If a teacher record is found, set the user type to Teacher
                $_SESSION['UserType'] = 'Teacher';
            else
                // Otherwise, assume the user is an Admin
                $_SESSION['UserType'] = 'Admin';
        }

        if ($user) {
            // Seperate this part into a function that 
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
                    $stmt = $pdo->prepare("  SELECT s.Subjectname, s.SubjectCode FROM subject s 
                                            INNER JOIN class c ON s.SubjectCode = c.SubjectCode
                                            INNER JOIN class_student cs ON cs.ClassId = c.ClassID
                                            WHERE cs.StudentID= ?;");
                    $stmt->bindParam(1, $user['UserID']);
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
                    $stmt = $pdo->prepare("SELECT DateJoined FROM administrator WHERE AdminID=?;");
                    $stmt->bindParam(1, $user['UserID']);
                    $stmt->execute();
                    $admin = $stmt->fetch(PDO::FETCH_ASSOC);

                    if ($admin) {
                        $_SESSION['DateJoined'] = $admin['DateJoined'];
                    }
                    header("Location: ../AdminPage/adminPage.php");
                    exit();
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