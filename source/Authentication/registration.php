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

            function pushErrorIfEmpty($variable, $errorsArray, $errorMessage)
            {
                if (empty($variable) || $variable == "")
                    array_push($errorsArray, $errorMessage);
            }

            function sanitizeInput($input)
            {
                if (isset($input)) {
                    // Remove leading and trailing spaces
                    $input = trim($input);
                    // Strip all slashes to prevent cross script attacks
                    $input = stripslashes($input);
                    // Invalidates html tags to prevent cross script attacks
                    $input = htmlspecialchars($input);
                }
                return $input;
            }

            function validateEmail($email, $errors)
            {
                if (!filter_var($email, FILTER_VALIDATE_EMAIL))
                    array_push($errors, "Email is NOT valid!");
                else {
                    // Check if email already exists
                    global $pdo;
                    $sTestEmail = $pdo->prepare('SELECT * FROM user WHERE Email = "' . $email . '";');
                    if ($sTestEmail->execute() == 0) {
                        $sTestEmail->fetchAll();
                        return true;
                    } else
                        array_push($errors, "Email already exists!");
                }

                return false;
            }

            function validatePassword($password, $repeatpassword, $errors)
            {
                // Equivalent one-line regex: /^(?=.*[A-Z])(?=.*\d).{5,}$/
                if ($password !== $repeatpassword)
                    array_push($errors, "Passwords do NOT match!");
                else if (strlen($password) < 5)
                    array_push($errors, "Password length must be minimum 5 characters long!");
                else if (!preg_match('/[A-Z]/', $password))
                    array_push($errors, "Password should contain at least 1 Uppercase!");
                else if (!preg_match('/[0-9]/', $password))
                    array_push($errors, "Password should contain at least 1 Number!");
                else
                    return true;
                return false;
            }

            if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                $usertype = sanitizeInput($_POST["user-type"]);
                $errors = array(); //array to store errors

                // Get and sanitize all general attributes from POST request
                $firstname = sanitizeInput($_POST["fname"]);
                $lastname = sanitizeInput($_POST["lname"]);
                $email = sanitizeInput($_POST["email"]);
                $gender = sanitizeInput($_POST["gender"]);
                $dateofbirth = sanitizeInput($_POST["dob"]);
                $password = sanitizeInput($_POST["password"]);
                $repeatpassword = sanitizeInput($_POST["repassword"]);

                // Validation of general attributes
                validateEmail($email, $errors);
                validatePassword($password, $repeatpassword, $errors);
                $passwordhash = password_hash($password, PASSWORD_DEFAULT); // Hashing of password

                // Get, sanitize & validate all specific attributes from POST request
                switch ($usertype) {
                    case "Student":
                        $classgroup = sanitizeInput(strtoupper($_POST["classGroup"]));
                        $level = sanitizeInput($_POST["level"]);
                        $subjects = explode(',', sanitizeInput($_POST["subjects"]));

                        pushErrorIfEmpty($classgroup, $errors, "Class-group cannot be blank!");
                        pushErrorIfEmpty($level, $errors, "Level cannot be blank!");
                        pushErrorIfEmpty($subjects, $errors, "No subjects selected!");

                        if (!filter_var($level, FILTER_VALIDATE_INT)) array_push($errors, "Level is NOT numeric!");
                        if (count($subjects) < 5) array_push($errors, "Select 5 subjects!");
                        break;
                    case "Teacher":
                        $subjecttaught = sanitizeInput($_POST["subjectTaught"]);
                        $datejoinedteacher = sanitizeInput($_POST["teacherDateJoined"]);

                        pushErrorIfEmpty($subjecttaught, $errors, "Subject taught cannot be blank!");
                        pushErrorIfEmpty($datejoinedteacher, $errors, "Date Joined cannot be empty!");
                        break;
                    case "Admin":
                        $datejoinedadmin = $_POST["adminDateJoined"];

                        pushErrorIfEmpty($datejoinedadmin, $errors, "Date Joined cannot be blank!");
                        break;
                }

                // If there were no errors with input, insert into database
                if (count($errors) == 0) {
                    switch ($usertype) {
                        case 'Student':
                            // Starts a transaction since I can't pass the subjects array in a stored procedure
                            $pdo->beginTransaction();
                            try {
                                $sInsertStudent = $pdo->prepare('CALL sp_addStudent(?, ?, ?, ?, ?, ?, ?, ?);');
                                $sInsertStudent->execute([$firstname, $lastname, $email, $gender, $dateofbirth, $passwordhash, $classgroup, $level]);
                                $studentID = $sInsertStudent->fetchAll(PDO::FETCH_NUM)[0];
                                $sInsertStudent->closeCursor();

                                $sGetClassID = $pdo->prepare('SELECT ClassID FROM class WHERE SubjectCode = ? AND Level = ? AND ClassGroup = ?;');
                                $sAssignClass = $pdo->prepare('INSERT INTO class_student(ClassID, StudentID) VALUES(?, ?);');

                                foreach ($subjects as $subject) {
                                    $sGetClassID->execute([$subject, $level, $classgroup]);
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
                            $sInsertTeacher->execute([$firstname, $lastname, $email, $gender, $dateofbirth, $passwordhash, $subjecttaught, $datejoinedteacher]);
                            break;
                        case 'Admin':
                            $sInsertAdmin = $pdo->prepare('CALL sp_addAdmin(?, ?, ?, ?, ?, ?, ?)');
                            $sInsertAdmin->execute([$firstname, $lastname, $email, $gender, $dateofbirth, $passwordhash, $datejoinedadmin]);
                            break;
                    }
                }

                if (count($errors) > 0) {
                    echo "<h2 style='text-align: center; color: rgb(53, 12, 12);  '>Registration Unsuccessfull </h2>";

                    foreach ($errors as $errors) {
                        echo "<div class='error-message' > $errors </div>";
                    }

                    echo "<a href='javascript:self.history.back()'>
                            <button class='indigoTheme roundBorder' style=' margin-top: 15px; border-width: 2px;'> 
                            GO BACK </button>";
                } else {
                    //displaying sucessful registraton status
                    echo "<h2 style='text-align: center; color: rgb(11, 91, 32); ;  '>Successfully registered!</h2>";
                    echo "<a href='javascript:self.history.back()'><button class='indigoTheme roundBorder' style=' margin-top: 15px; border-width: 2px; font-size:25px;'> Click to here Sign in! </button>";
                }
            }
            ?>
        </div>
    </body>

    </html>