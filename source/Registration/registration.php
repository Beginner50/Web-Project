    <!DOCTYPE html>
    <html lang="en">

    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Document</title>

        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
        <link rel="stylesheet" href="../stylesheets/common.css">
    </head>

    <body style="background-color: var(--duskSky);">

        <div class="container">
            <?php
            ini_set('display_startup_errors', 1);
            ini_set('display_errors', 1);
            error_reporting(-1);

            include '../connect.php';

            if (isset($_POST["registrationSubmit-buttonCheck"])) {

                //general attributes
                $firstname = $_POST["fname"];
                $lastname = $_POST["lname"];
                $email = $_POST["email"];
                $gender = $_POST["gender"];
                $dateofbirth = $_POST["dob"];
                $password = $_POST["password"];
                $repeatpassword = $_POST["repassword"];

                $passwordhash = password_hash($password, PASSWORD_DEFAULT); //Hashing of password
                $errors = array(); //array to store errors
                $usertype = $_POST["user-type"];
                $authtype = 'student'; //default authentication type in student table

                //specific attributes
                if ($usertype == "Student") {
                    $classgroup = $_POST["classGroup"];
                    $level = $_POST["level"];
                    $subjects = $_POST["subjects"];

                    //Specific attribute validation
                    if ($classgroup == "") {
                        array_push($errors, "Class-group cannot be blank!");
                    }
                    if ($level == "") {
                        array_push($errors, "Please select your level!");
                    }

                    if (!empty($_POST['subjects'])) {

                        // Convert it into an array to use for database input
                        $subjectsArray = explode(',', $subjects);
                    } else {
                        array_push($errors, "No subjects selected!");
                    }
                } elseif ($usertype == "Teacher") {

                    $subjecttaught = $_POST["subjectTaught"];
                    $datejoinedteacher = $_POST["teacherDateJoined"];

                    if (empty($subjecttaught)) {
                        array_push($errors, "Subject taught cannot be empty!");
                    }
                    if (empty($datejoinedteacher)) {
                        array_push($errors, "Please input the date you have joined!");
                    }
                } elseif ($usertype == "Admin") {

                    $datejoinedadmin = $_POST["adminDateJoined"];

                    if (empty($datejoinedadmin)) {
                        array_push($errors, "Please input the date you have joined!");
                    }
                }

                //general validation
                if (empty($firstname) or empty($lastname) or empty($email) or empty($gender) or empty($password) or empty($repeatpassword)) {

                    array_push($errors, "All fields are required!");
                }

                if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                    array_push($errors, "Email address is NOT valid");
                } else {

                    $verifyemail = mysqli_query($conn, "SELECT Email FROM user WHERE Email='$email' ");

                    //checks if query  returns anything
                    if (mysqli_num_rows($verifyemail) != 0) {
                        array_push($errors, "Email already exist! Use another email");
                    }
                }

                if (strlen($password) < 5) {
                    array_push($errors, "Password length must be minimum 5 characters long.");
                }
                if (!preg_match('/[A-Z]/', $password)) {

                    array_push($errors, "Password should contain at least 1 Uppercase ");
                }
                if (!preg_match('/[0-9]/', $password)) {

                    array_push($errors, "Password should contain at least 1 Number ");
                }



                if ($password != $repeatpassword) {
                    array_push($errors, "Password does NOT match!");
                }

                //checking for errors
                if (count($errors) > 0) {

                    echo "<h2 style='text-align: center; color: rgb(53, 12, 12);  '>Registration Unsuccessfull </h2>";

                    foreach ($errors as $errors) {
                        echo "<div class='error-message' > $errors </div>";
                    }

                    echo "<a href='javascript:self.history.back()'>
                 <button class='indigoTheme roundBorder' style=' margin-top: 15px; border-width: 2px;'> 
                 GO BACK </button>";
                } else {

                    //INSERTING INTO USER
                    $sqlquery = "INSERT INTO user(DateOfBirth,FirstName,LastName,Email,Gender,Password,AuthorisationType) VALUES (?,?,?,?,?,?,?)";
                    $stmt = mysqli_stmt_init($conn); //initialises connection

                    if (mysqli_stmt_prepare($stmt, $sqlquery)) {

                        //binds and execute statement
                        mysqli_stmt_bind_param($stmt, "sssssss", $dateofbirth, $firstname, $lastname, $email, $gender, $passwordhash, $authtype);
                        mysqli_stmt_execute($stmt);

                        //searching maximum userid- most recent insert
                        $sqlquery = "SELECT MAX(Userid) AS max_userid FROM user";
                        $result = mysqli_query($conn, $sqlquery);

                        if ($result) {
                            // Fetch the result as an associative array
                            $row = mysqli_fetch_assoc($result);
                            $max_userid = $row['max_userid'];
                        } else {
                            echo "Error: " . mysqli_error($conn);
                        }


                        //INSERTING FOR THE DIFFERENT USER TYPES
                        mysqli_stmt_close($stmt); //must reinitialise stmt
                        $stmt = mysqli_stmt_init($conn);

                        if ($usertype == 'Student') {

                            //INSERTING INTO STUDENT
                            $sqlquery = "INSERT INTO student(StudentID,Level,ClassGroup) VALUES (?,?,?)";
                            if (mysqli_stmt_prepare($stmt, $sqlquery)) {

                                //binds and execute statement
                                mysqli_stmt_bind_param($stmt, "iis", $max_userid, $level, $classgroup);
                                mysqli_stmt_execute($stmt);

                                //INSERTING INTO CLASS_STUDENT
                                mysqli_stmt_close($stmt); //must reinitialise stmt
                                $stmt = mysqli_stmt_init($conn);

                                $sqlquery = "INSERT INTO class_student(Level,ClassGroup,SubjectCode,StudentID) VALUES (?,?,?,?)";
                                if (mysqli_stmt_prepare($stmt, $sqlquery)) {

                                    foreach ($subjectsArray as $subject) {
                                        mysqli_stmt_bind_param($stmt, "issi", $level, $classgroup, $subject, $max_userid);
                                        mysqli_stmt_execute($stmt);
                                    }
                                } else {
                                    die("Something went wrong in class_student table!");
                                }
                            } else {
                                die("Something went wrong in student table!");
                            }
                            mysqli_stmt_close($stmt); //must close stmt
                        } else if ($usertype == 'Teacher') {

                            //INSERTING INTO TEACHER
                            $stmt = mysqli_stmt_init($conn);

                            $sqlquery = "INSERT INTO teacher(TeacherID,subjectTaught,DateJoined) VALUES (?,?,?)";
                            if (mysqli_stmt_prepare($stmt, $sqlquery)) {

                                mysqli_stmt_bind_param($stmt, "iss", $max_userid, $subjecttaught, $datejoinedteacher);
                                mysqli_stmt_execute($stmt);
                            } else {
                                die("Something went wrong in teacher table!");
                            }
                            mysqli_stmt_close($stmt); //must close stmt
                        } else if ($usertype == 'Admin') {
                            //INSERTING INTO ADMIN
                            $stmt = mysqli_stmt_init($conn);
                            $sqlquery = "INSERT INTO administrator(AdminID,DateJoined) VALUES (?,?)";
                            if (mysqli_stmt_prepare($stmt, $sqlquery)) {

                                mysqli_stmt_bind_param($stmt, "is", $max_userid, $datejoinedadmin);
                                mysqli_stmt_execute($stmt);
                            }
                        }

                        //displaying sucessful registraton status
                        echo "<h2 style='text-align: center; color: rgb(11, 91, 32); ;  '>Successfully registered!</h2>";
                        echo "<a href='javascript:self.history.back()'><button class='indigoTheme roundBorder' style=' margin-top: 15px; border-width: 2px; font-size:25px;'> Click to here Sign in! </button>";
                    } else {
                        die("Something went wrong in user table!");
                    }
                }
            }
            ?>
        </div>

        <script src="../scripts/jQuery.js"></script>
    </body>

    </html>