<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Personal Info</title>

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="../../stylesheets/common.css">
    <link rel="stylesheet" href="../../stylesheets/registration.css">
</head>
<body style="background-color: var(--duskSky);">
    
    <div class="container">
        <?php
        ini_set('display_startup_errors', 1);
        ini_set('display_errors', 1);
        error_reporting(-1);

        include '../connect.php';

        if (isset($_POST["personalinfo-savechanges"])){
            
            //general attributes
            $firstname = $_POST["firstname"];
            $lastname = $_POST["lastname"];
            $email = $_POST["email"];
            $gender = $_POST["gender"];
            $dateofbirth = $_POST["dateofbirth"];
     
           
            $errors = array(); //array to store errors


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

            //checking for errors
            if (count($errors) > 0) {

                echo "<h2 style='text-align: center; color: rgb(53, 12, 12);  '>Registration Unsuccessfull </h2>";

                foreach ($errors as $errors) {
                    echo "<div class='error-message' > $errors </div>";
                }

                echo "<a href='javascript:self.history.back()'>
                <button class='indigoTheme roundBorder' style=' margin-top: 15px; border-width: 2px;'> 
                GO BACK </button>";
            }
            else{
                 //displaying sucessful registraton status
                 echo "<h2 style='text-align: center; color: rgb(11, 91, 32); ;  '>Successfully Saved Changes!</h2>";
                 echo "<a href='javascript:self.history.back()'><button class='indigoTheme roundBorder' style=' margin-top: 15px; border-width: 2px; font-size:25px;'> Back </button>";
            }


            
        }
        ?>
    </div>
</body>
</html>