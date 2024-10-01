<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Account Management</title>

    <link rel="stylesheet" href="stylesheets/accountManagementPage/Acc_management.css">
    <link rel="stylesheet" href="stylesheets/common.css">
    <link rel="stylesheet" href="stylesheets/authenticationPage/common.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css">
    <link rel="stylesheet" href="stylesheets/partials/navBar.css">
    <link rel="stylesheet" href="stylesheets/partials/sidebar.css">
    <script>
        0
    </script>
</head>
<?php
$page = 'accountManagementTab';
require 'partials/navBar.php';
?>

<body>
    <div class="wrapper">
        <!--  Side Navigation bar -->
        <aside class="sidebar">
            <h2>Account management</h2>
            <ul>
                <li><a href="#main-content"><i class="fa fa-user" aria-hidden="true"></i>UserID</a></li>
                <li><a href="#userID-content"><i class="fa fa-address-card" aria-hidden="true"></i>Personal Information</a></li>
                <li><a href="#personalinfo-savechanges" style="font-size:12px;"><i class="fa fa-graduation-cap" aria-hidden="true"></i>Student/Teacher Information</a></li>
                <li><a href="#loginmanagement"><i class="fa fa-key" aria-hidden="true"></i>Login Management</a></li>
                <li><a href="#logout-content"><i class="fa fa-sign-out" aria-hidden="true"></i>Log out </a></li>
            </ul>
        </aside>

        <div id="main-content" class="main-content">

            <!--USERID information  -->
            <div class="userID-content first-column">
                <h3>UserID:</h3>
                <div id="userID-content" class="information description">Your UserID is used to uniquely identify yourself within the database. </div>
            </div>
            <div class="userID-content2 second-column">
                <div class="information" style="width: 200px;">
                    <div class="sub-information">UserID</div>
                    <div class="information-input"> <?php echo $_SESSION['UserID']; ?> </div>
                </div>

            </div>

            <!--Personal information  -->
            <div class="personalinfo-content first-column">
                <h2>Personal Information</h2>
                <div class="information description">This is your private general information that is stored and will be used only for educative purposes.</div>
            </div>
            <div class="personalinfo-content2  second-column">
                <form id="personalinfo-form" class="personalinfo-grid" method="post" action="AccountManagement/personalinfo.php">
                    <div class="information">
                        <label class="sub-information">First Name: </label>
                        <input class="information-input" type="text" id="firstname" name="firstname" value="<?php echo $_SESSION['FirstName']; ?>">
                    </div>
                    <div class="information">
                        <label class="sub-information" for="lastname">Last Name: </label>
                        <input class="information-input" type="text" id="lastname" name="lastname" value="<?php echo $_SESSION['LastName']; ?>">
                    </div>
                    <!--  -->

                    <div class="information">
                        <label class="sub-information" for="gender">Gender: </label>
                        <input class="information-input" type="text" id="gender" name="gender" value="<?php echo $_SESSION['Gender']; ?>"> <!-- must do a dropdown menu like in register -->

                    </div>
                    <div class="information">
                        <label class="sub-information" for="dateofbirth">Date Of Birth: </label>
                        <input class="information-input" type="date" id="dateofbirth" name="dateofbirth" value="<?php echo date('Y-m-d', strtotime($_SESSION['DateOfBirth'])); ?>"> <!-- Formats date in proper format -->

                    </div>
                    <div class="information personalinfo-email">
                        <label class="sub-information" for="email">Email: </label>
                        <input class="information-input" type="email" id="email" name="email" value="<?php echo $_SESSION['Email']; ?>" style="width:300px;">
                    </div>

                    <button type="submit" id="personalinfo-savechanges" name="personalinfo-savechanges" class="indigoTheme roundBorder savebutton" form="personalinfo-form">Save Changes</button>
            
                </form>
                <?php

                // Check if there are any errors in the session
                if (isset($_SESSION['errors']) && !empty($_SESSION['errors'])) {

                    foreach ($_SESSION['errors'] as $error) {
                        echo '<div class="error-container">' . $error . '</div>'; // Display each error
                    }

                    // Unset the errors after displaying them
                    unset($_SESSION['errors']);
                }

                if(isset($_SESSION['Success'])){

                    echo '<div class="success-container" >' . $_SESSION['Success'] . '</div>';
                    unset($_SESSION['Success']);
                }
                ?>

            </div>

            <!--Displaying either student or teacher information-->
            <?php if (empty($_SESSION['Level']) && empty($_SESSION['ClassGroup'])): //Checks if it is a student or a teacher
            ?>

                <!--Teacher information  -->
                <div id="teacherinformation" class="teacherinformation-content first-column">
                    <h2>Teacher information</h2>
                    <div id="teacherinformation-description" class="information description">This is your private teacher information that is stored and will be used only for educative purposes.</div>
                </div>
                <div class="teacherinformation-content2  second-column">
                    <div class="information">
                        <div class="sub-information">Teaching subject:</div>
                        <div class="information-input"> <?php echo $_SESSION['SubjectTaught']; ?> </div>
                    </div>
                    <div class="information">
                        <div class="sub-information">Date You joined EduPortal</div>
                        <div class="information-input"> <?php echo date('jS F Y', strtotime($_SESSION['DateOfBirth'])) . "  "; //displays datejoined in words 
                                                        ?> </div>
                        <div class="information-input"> <?php echo "(" . (new DateTime($_SESSION['DateOfBirth']))->diff(new DateTime())->days . " days ago)"; //displays number of days that have passed
                                                        ?>
                        </div>
                    </div>
                </div>

            <?php else: ?>
                <!--Student information  -->
                <div id="studentinfo" class="studentinformation-content first-column">
                    <h2>Student information</h2>
                    <div id="studentinfo-description" class="information description">This is your private student information that is stored and will be used only for educative purposes.</div>
                </div>
                <div class="studentinformation-content2  second-column">
                    <div class="information">
                        <div class="sub-information">Level</div>
                        <div class="information-input"> <?php echo $_SESSION['Level']; ?> </div>
                    </div>
                    <div class="information">
                        <div class="sub-information">Class Group</div>
                        <div class="information-input"> <?php echo $_SESSION['ClassGroup']; ?> </div>
                    </div>

                    <div class="information studentinfo-grid">
                        <div class="sub-information">Subjects Taken</div>
                        <div class="subjectstaken">
                        <?php 
                        foreach($_SESSION['Subjects'] as $Subjects){
                            echo '<div class="subject-item">';
                            echo '<div class="subject-code">'. $Subjects['SubjectCode'] .'</div>';
                            echo '<div class="subject-name">'. $Subjects['Subjectname'] .'</div>';
                            echo '</div>';
                        }
                        ?>
                    </div>
                    </div>
                </div>
            <?php endif; ?>

            <!--login management  -->
            <div id="loginmanagement" class="login-content first-column">
                <h2>Login Management</h2>
                <div class="information description">We recommend that you periodically update your password to help prevent unauthorized access to your account.</div>
            </div>
            <div class="login-content2  second-column">
                <h2 style="margin-bottom: 10px;">Change Password</h2>
                <div class="inputpassword">
                    <form id="passwordchange-form" class="inputpassword" method="post" action="AccountManagement/passchange.php">

                        <div class="login-passwordbox">
                            <div class="login-password">
                                <input type="password" name="currentpassword" id="password" placeholder=" " required>
                                <label for="password">Password</label>
                                <img src="icons/eye-close.png" id="toggle-password" onclick="togglePasswordVisibility('password','toggle-password')">
                            </div>
                        </div>

                        <div class="login-passwordbox">
                            <div class="login-password">
                                <input type="password" required placeholder=" " name="newpassword" id="newpassword">
                                <label>New Password</label>
                                <img src="icons/eye-close.png" id="toggle-newpassword" onclick="togglePasswordVisibility('newpassword','toggle-newpassword')">
                            </div>
                        </div>

                        <div class="login-passwordbox">
                            <div class="login-password">
                                <input type="password" required placeholder=" " name="renewpassword" id="renewpassword">
                                <label>Confirm New Password</label>
                                <img src="icons/eye-close.png" id="toggle-renewpassword" onclick="togglePasswordVisibility('renewpassword','toggle-renewpassword')">
                            </div>
                        </div>
                        <button type="submit" id="personalinfo-changepassword" name="personalinfo-changepassword" class="indigoTheme roundBorder savebutton" form="passwordchange-form" style="margin-left: 50px;">Save Changes</button>
                    </form>

                </div>
            </div>

            <!--logout information  -->
            <div class="logout-content first-column">
                <h3>Log out!</h3>
                <div id="logout-content" class="information description">Worried that your account or password has been compromised? You can forcibly log out from all devices. </div>
            </div>
            <div class="logout-content2 second-column">

                <form action="AccountManagement/logout.php">
                    <button type="submit" class="indigoTheme roundBorder savebutton">Log out Everywhere</button>
                </form>
                        
            </div>
        </div>
    </div>

    <script src="scripts/accManagementPage.js"></script>

</body>

</html>