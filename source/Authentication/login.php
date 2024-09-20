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
            ini_set('display_startup_errors', 1);
            ini_set('display_errors', 1);
            error_reporting(-1);

            session_start();
            include '../connect.php';

                $email=$_POST["email"];
                $password=$_POST["password"];

                $sql="SELECT * FROM user WHERE Email='$email'";
                $result=mysqli_query($conn,$sql); //queries connection
                $user=mysqli_fetch_array($result,MYSQLI_ASSOC); //Places result in an associative array

                if($user){

                    if(password_verify($password,$user["Password"])){

                        $_SESSION['Email'] = $user['Email'];
                        $_SESSION['Password'] = $user['Password'];

                        $_SESSION['UserID'] = $user['UserID'];
                        $_SESSION['DateOfBirth'] = $user['DateOfBirth'];
                        $_SESSION['FirstName'] = $user['FirstName'];
                        $_SESSION['LastName'] = $user['LastName'];
                        $_SESSION['Gender'] = $user['Gender'];

                        // query the student table using UserID  to get additional data
                        $userID = $user['UserID']; 
                        $sql = "SELECT Level, ClassGroup FROM student WHERE StudentID='$userID'";
                        $result = mysqli_query($conn, $sql);
                        $student = mysqli_fetch_array($result, MYSQLI_ASSOC);

                        if ($student) {  //is $student is true

                            // User is a student
                            $_SESSION['Level'] = $student['Level'];
                            $_SESSION['ClassGroup'] = $student['ClassGroup'];

                            //retrieving subjects taken

                            $sql="  SELECT s.SubjectName,cs.SubjectCode
                                    FROM class_student cs
                                    INNER JOIN subject s ON cs.SubjectCode = s.SubjectCode
                                    WHERE cs.StudentID = $userID;  
                                ";
                               
                            // Prepare and bind
                            $stmt = $conn->prepare($sql);
                            $stmt->bind_param("i", $userID); // "i" denotes an integer parameter

                            // Execute the query
                            $stmt->execute();
                            $result = $stmt->get_result();

                            // Fetch the results
                            if ($result->num_rows > 0) {

                                //loops through $results()
                                while ($row = $result->fetch_assoc()) {
                                    echo "Subject Name: " . $row['SubjectName'] . "<br>";
                                }
                            } else {
                                echo "No subjects found for this student.";
                            }

                            $stmt->close();

                        } else {

                            // Check if the user is a teacher (exists in 'teacher' table)
                            $sql = "SELECT SubjectTaught, DateJoined FROM teacher WHERE TeacherID='$userID'";
                            $result = mysqli_query($conn, $sql);
                            $teacher = mysqli_fetch_array($result, MYSQLI_ASSOC);
                            
                            if ($teacher) {
                                // User is a teacher
                                $_SESSION['SubjectTaught'] = $teacher['SubjectTaught'];
                                $_SESSION['DateJoined'] = $teacher['DateJoined'];
                            } 
                        }
                        header("Location: ../accManagementPage.php");
                        exit();
             
    
                    }else{
                        echo "<h2 style='text-align: center; color: rgb(53, 12, 12);  '>Login Unsuccessfull </h2>";
                        echo"<div class='alert alert-danger'>Password does NOT match!</div>";
                        echo "<a href='javascript:self.history.back()'><button class='indigoTheme roundBorder' style=' margin-top: 15px; border-width: 4px; font-size:25px; padding:0px 15px;'> Back </button>";
                    }
                }else{
                    echo "<h2 style='text-align: center; color: rgb(53, 12, 12);  '>Login Unsuccessfull </h2>";
                    echo "<div class='alert alert-danger'>Email does NOT exist!</div>";
                    echo "<a href='javascript:self.history.back()'><button class='indigoTheme roundBorder' style=' margin-top: 15px; border-width: 4px; font-size:25px; padding:0px 15px;'> Back </button>";
                }
            ?>
        </div>
    </body>
</html>