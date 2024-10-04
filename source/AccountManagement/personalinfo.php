<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Personal Info</title>

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" ">
    <link rel="stylesheet" href="../../stylesheets/common.css">
    <link rel="stylesheet" href="../../stylesheets/authenticationPage/common.css">
</head>
<body style="background-color: var(--duskSky);">
    
    <div class="container">
        <?php
        ini_set('display_startup_errors', 1);
        ini_set('display_errors', 1);
        error_reporting(-1);

        include '../connect.php';

        if (isset($_POST["personalinfo-savechanges"]) OR isset($_POST["personalinfo-savechanges-admin"])){
            
            //general attributes
            $firstname = $_POST["firstname"];
            $lastname = $_POST["lastname"];
            $email = $_POST["email"];
            $gender = $_POST["gender"];
            $dateofbirth = $_POST["dateofbirth"];
     
            $UserID=$_SESSION['UserID'];
            $errors = array(); //array to store errors


            //general validation
            if (empty($firstname) or empty($lastname) or empty($email) or empty($gender)) {

                array_push($errors, "All fields are required!");
            }

            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                array_push($errors, "Email address is NOT valid");
            } else {
                if($email != $_SESSION['Email']){ //if user has changed current email, we must check if the new email already exist in db

                    $verifyemail = mysqli_query($conn, "SELECT Email FROM user WHERE Email='$email' ");

                    //checks if query  returns anything
                    if (mysqli_num_rows($verifyemail) != 0) {
                        array_push($errors, "Email already exist! Use another email");
                    }

                }
    
            }

            //checking for errors
            if (count($errors) > 0) {
                $_SESSION['errors'] = $errors; 
                if(isset($_POST["personalinfo-savechanges"]) ){
                    header('Location: ../accManagementPage.php#userID-content');
                    exit();
                }else if(isset($_POST["personalinfo-savechanges-admin"]) ) {
                    header('Location: ../adminPage.php');
                    exit();
                }
            }
            else{
                 // Updating changes into User table

                 $sqlquery = "UPDATE user
                            SET DateOfBirth = ?,
                                FirstName = ?,
                                LastName = ?,
                                Email = ?,
                                Gender = ?
                            WHERE UserId = ?";

             
                $stmt = $pdo->prepare($sqlquery);

                $stmt->bindParam(1, $dateofbirth, PDO::PARAM_STR);
                $stmt->bindParam(2, $firstname, PDO::PARAM_STR);
                $stmt->bindParam(3, $lastname, PDO::PARAM_STR);
                $stmt->bindParam(4, $email, PDO::PARAM_STR);
                $stmt->bindParam(5, $gender, PDO::PARAM_STR);
                $stmt->bindParam(6, $UserID, PDO::PARAM_INT); 

                // Execute the statement
                if ($stmt->execute()) {
                    // Re-fetch updated user information from the database
                    $stmt = $pdo->prepare('SELECT * FROM user WHERE UserID = ?');
                    $stmt->execute([$_SESSION['UserID']]);
                    $updatedUser = $stmt->fetch(PDO::FETCH_ASSOC);

                    // Update the session with the new information
                    $_SESSION['FirstName'] = $updatedUser['FirstName'];
                    $_SESSION['LastName'] = $updatedUser['LastName'];
                    $_SESSION['Email'] = $updatedUser['Email'];
                    $_SESSION['DateOfBirth'] = $updatedUser['DateOfBirth'];
                    $_SESSION['Gender'] = $updatedUser['Gender'];

                    // Set a success message and redirect to the account management page
                    $_SESSION['Success'] = "Changes Successful";

                    if(isset($_POST["personalinfo-savechanges"]) ){
                        header('Location: ../accManagementPage.php#userID-content');
                        exit();
                    }else if(isset($_POST["personalinfo-savechanges-admin"]) ) {
                        header('Location: ../adminPage.php');
                        exit();
                    }
            
                }
            
            }
        }
        ?>
    </div>
</body>
</html>