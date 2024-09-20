<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>

    
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="../../stylesheets/common.css">
    <link rel="stylesheet" href="../../stylesheets/authenticationPage/common.css">
</head>
<body>
    <div class="container">
            <?php
            ini_set('display_startup_errors', 1);
            ini_set('display_errors', 1);
            error_reporting(-1);

            include '../connect.php';

            $currentpass = $_POST["currentpassword"];
            $newpassword = $_POST["newpassword"];
            $renewpassword = $_POST["renewpassword"];

            $errors = array(); //array to store errors
            if (isset($_POST["personalinfo-changepassword"])){

                  //general validation
                  if (empty($newpassword) or empty($renewpassword) or empty($currentpass)) {

                    array_push($errors, "All fields are required!");
                }

                if (strlen($newpassword) < 5) {
                    array_push($errors, "Password length must be minimum 5 characters long.");
                }
                if (!preg_match('/[A-Z]/', $newpassword)) {

                    array_push($errors, "Password should contain at least 1 Uppercase ");
                }
                if (!preg_match('/[0-9]/', $newpassword)) {

                    array_push($errors, "Password should contain at least 1 Number ");
                }

                
                if ($newpassword != $renewpassword) {
                    array_push($errors, "Password does NOT match!");
                }
                
                if (count($errors) > 0) {

                    echo "<h2 style='text-align: center; color: rgb(53, 12, 12);  '>Password Change Unsuccessfull </h2>";

                    foreach ($errors as $errors) {
                        echo "<div class='error-message' > $errors </div>";
                    }

                    echo "<a href='javascript:self.history.back()'>
                 <button class='indigoTheme roundBorder' style=' margin-top: 15px; border-width: 2px;'> 
                 GO BACK </button>";
                } else {
                    //displaying sucessful registraton status
                    echo "<h2 style='text-align: center; color: rgb(11, 91, 32); ;  '>Successfully Changed Password!</h2>";
                    echo "<a href='javascript:self.history.back()'><button class='indigoTheme roundBorder' style=' margin-top: 15px; border-width: 2px; font-size:25px;'> Click to here Sign in! </button>";

                }

            }

            ?>
    </div>
</body>
</html>