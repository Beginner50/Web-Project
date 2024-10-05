    <!DOCTYPE html>
    <html lang="en">

    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Document</title>

        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
        <link rel="stylesheet" href="../stylesheets/common.css">
        <link rel="stylesheet" href="../stylesheets/authenticationPage/common.css">
    </head>

    <body style="background-color: var(--duskSky);">

        <div class="container">
            <?php
            ini_set('display_startup_errors', 1);
            ini_set('display_errors', 1);
            error_reporting(-1);

            include '../connect.php';

            function validateAndSanitizeGeneralAttributes() {}

            function validateAndSanitizeSpecificAttributes() {}

            if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                $errors = array(); //array to store errors

                //general attributes
                $firstname = $_POST["fname"];
                $lastname = $_POST["lname"];
                $email = $_POST["email"];
                $gender = $_POST["gender"];
                $dateofbirth = $_POST["dob"];
                $password = $_POST["password"];
                $repeatpassword = $_POST["repassword"];

                $passwordhash = password_hash($password, PASSWORD_DEFAULT); //Hashing of password
                $usertype = $_POST["user-type"];
                $authtype = 'student'; //default authentication type in student table

                //specific attributes
                if ($usertype == "Student") {
                    $classgroup = strtoupper($_POST["classGroup"]);
                    $level = $_POST["level"];
                    $subjects = $_POST["subjects"];

                    //Specific attribute validation
                    if ($classgroup == "")
                        array_push($errors, "Class-group cannot be blank!");
                    if ($level == "")
                        array_push($errors, "Please select your level!");

                    // Convert it into an array to use for database input
                    if (!empty($_POST['subjects']))
                        $subjectsArray = explode(',', $subjects);
                    else
                        array_push($errors, "No subjects selected!");
                } elseif ($usertype == "Teacher") {
                    $subjecttaught = $_POST["subjectTaught"];
                    $datejoinedteacher = $_POST["teacherDateJoined"];

                    if (empty($subjecttaught))
                        array_push($errors, "Subject taught cannot be empty!");
                    if (empty($datejoinedteacher))
                        array_push($errors, "Please input the date you have joined!");
                } elseif ($usertype == "Admin") {
                    $datejoinedadmin = $_POST["adminDateJoined"];
                    if (empty($datejoinedadmin))
                        array_push($errors, "Please input the date you have joined!");
                }
                
                function GeneralValidation($firstname,$lastname,$email,$gender,&$errors,$conn){
                    //general validation
                    if (empty($firstname) or empty($lastname) or empty($email) or empty($gender)) {

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
                }
                
                function PasswordCheck($password,&$errors){
                    if (strlen($password) < 5) {
                        array_push($errors, "Password length must be minimum 5 characters long.");
                    }
                    if (!preg_match('/[A-Z]/', $password)) {
    
                        array_push($errors, "Password should contain at least 1 Uppercase ");
                    }
                    if (!preg_match('/[0-9]/', $password)) {
    
                        array_push($errors, "Password should contain at least 1 Number ");
                    }
                }
                GeneralValidation($firstname,$lastname,$email,$gender,$errors,$conn);
                PasswordCheck($password,$errors);
     



                if ($password != $repeatpassword) {
                    array_push($errors, "Password does NOT match!");


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
                        // INSERTING INTO USER
                        $sqlquery = "INSERT INTO user (DateOfBirth, FirstName, LastName, Email, Gender, Password) VALUES (?, ?, ?, ?, ?, ?)";
                        $stmt = $pdo->prepare($sqlquery);
                        $stmt->execute([$dateofbirth, $firstname, $lastname, $email, $gender, $passwordhash]);

                        // Fetch the most recently inserted UserID
                        $sqlquery = "SELECT MAX(UserID) AS max_userid FROM user";
                        $stmt = $pdo->query($sqlquery);
                        $row = $stmt->fetch(PDO::FETCH_ASSOC); // Fetch the result as an associative array

                        if ($row)
                            $max_userid = $row['max_userid']; // Get the maximum user ID

                        if ($usertype == 'Student') {
                            // INSERTING INTO STUDENT TABLE
                            $stmt = $pdo->prepare('INSERT INTO student(StudentID,Level,ClassGroup) VALUES (?,?,?);');
                            $stmt->bindParam(1, $max_userid, PDO::PARAM_INT);
                            $stmt->bindParam(2, $level, PDO::PARAM_INT);
                            $stmt->bindParam(3, $classgroup, PDO::PARAM_STR);

                            if ($stmt->execute()) {
                                //INSERTING INTO CLASS_STUDENT TABLE

                                //FETCHING THE SUBJECT CODE FOR EACH SUBJECT NAME TAKEN
                                $placeholders = implode(',', array_fill(0, count($subjectsArray), '?'));
                                $sqlquery = "SELECT SubjectCode FROM subject WHERE SubjectName IN ($placeholders)";
                                $stmt = $pdo->prepare($sqlquery);

                                // Passes subjectsArray as values to be bound by placeholders
                                $stmt->execute($subjectsArray);
                                $subjectCode = $stmt->fetchAll(PDO::FETCH_COLUMN);

                                //FETCHING THE CORRESPONDING CLASSID
                                $placeholders = implode(',', array_fill(0, count($subjectCode), '?'));

                                $sqlquery = "SELECT ClassID 
                                         FROM class 
                                         WHERE SubjectCode IN ($placeholders) 
                                         AND Level = ? 
                                         AND ClassGroup = ?";


                                $stmt = $pdo->prepare($sqlquery);
                                $stmt->execute(array_merge($subjectCode, [$level, $classgroup]));

                                $classIds = $stmt->fetchAll(PDO::FETCH_COLUMN);

                                // INSERTING IN CLASS_STUDENT FOR EACH CLASSID
                                foreach ($classIds as $classId) {
                                    $sqlquery = "INSERT INTO class_student(ClassID,StudentID) VALUES (?,?);";
                                    $stmt = $pdo->prepare($sqlquery);
                                    $stmt->execute([$classId, $max_userid]);
                                }
                            } else {
                                die("Something went wrong in student table!");
                            }
                        } else if ($usertype == 'Teacher') {

                            // INSERTING INTO APPROVAL
                            $sqlquery = "INSERT INTO approval (AdminID, UserID, UserType, IsApproved) VALUES (?, ?, ?, ?)";
                            $stmt = $pdo->prepare($sqlquery);

                            $AdminID = null;
                            $IsApproved = 0;

                            $stmt->execute([$AdminID, $max_userid, $usertype, $IsApproved]);

                            // INSERTING INTO TEACHER
                            $sqlquery = "INSERT INTO teacher (TeacherID, subjectTaught, DateJoined) VALUES (?, ?, ?)";
                            $stmt = $pdo->prepare($sqlquery);

                            // Execute the statement
                            $stmt->execute([$max_userid, $subjecttaught, $datejoinedteacher]);
                        } else if ($usertype == 'Admin') {
                            // INSERTING INTO APPROVAL
                            $sqlquery = "INSERT INTO approval (AdminID, UserID, UserType, IsApproved) VALUES (?, ?, ?, ?)";
                            $stmt = $pdo->prepare($sqlquery);

                            // For testing purposes only (Make proper ammends to variables)
                            $AdminID = null;
                            $IsApproved = 1;
                            $stmt->execute([$AdminID, $max_userid, $usertype, $IsApproved]);

                            // INSERTING INTO ADMIN
                            $sqlquery = "INSERT INTO administrator (AdminID, DateJoined) VALUES (?, ?)";
                            $stmt = $pdo->prepare($sqlquery);
                            $stmt->execute([$max_userid, $datejoinedadmin]);
                        }

                        //displaying sucessful registraton status
                        echo "<h2 style='text-align: center; color: rgb(11, 91, 32); ;  '>Successfully registered!</h2>";
                        echo "<a href='javascript:self.history.back()'><button class='indigoTheme roundBorder' style=' margin-top: 15px; border-width: 2px; font-size:25px;'> Click to here Sign in! </button>";
                    }
                }
            }
            ?>
        </div>

        <script src="../scripts/jQuery.js"></script>
    </body>

    </html>