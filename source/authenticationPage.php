<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="School Website for ABC Academy">
    <link rel="stylesheet" href="stylesheets/common.css">
    <link rel="stylesheet" href="stylesheets/authenticationPage/common.css">
    <link rel="stylesheet" href="stylesheets/authenticationPage/callToAction.css">
    <link rel="stylesheet" href="stylesheets/authenticationPage/registrationFormGeneral.css">
    <link rel="stylesheet" href="stylesheets/authenticationPage/registrationFormSpecific.css">
    <title>School Website</title>

    <script>
        /*to prevent Firefox FOUC, this must be here*/
        let FF_FOUC_FIX;
    </script>

</head>

<body>
    <!-- Navigation Bar -->
    <?php $page = 'authenticationPage';
    require 'partials/navBar.php' ?>

    <!-- Main Wrapper -->
    <div id="main-wrapper" class="roundBorder-15">
        <!-- Call To Action Wrapper -->
        <div id="callToAction-wrapper" class="registrationCTA">
            <h1 class="callToAction">
                New to Education
                Portal?</h1>
            <button class="callToAction"> <a>Sign Up</a></button>
        </div>
        <!-- Form Wrapper -->
        <div id="form-wrapper">
            <!-- Sign In/Up Header -->
            <h1>Sign In</h1>

            <!-- Login Form (Default: on) -->
            <form id="login-form" method="post" action="Authentication/login.php">
                <div class="input-group"> Email: <input class="input-box" type="email" name="email" required
                        autocomplete="email">
                </div>
           
                <div class="input-group"> Password: <input class="input-box" type="password" name="password" id="login-password"required>
                </div>

            
                <button id="loginSubmit-button" type="submit" class="indigoTheme roundBorder" form="login-form">
                    Submit
                </button>
            </form>

            <!-- Registration Form (Default: off)-->
            <form id="registration-form" method="post" action="Authentication/registration.php"
                style="display: none;">
                <!-- User Type Fieldset (Select between different users) -->
                <fieldset id="userType-fieldset">
                    <input id="student-button" class="indigoTheme active noGap" type="button" form="none"
                        value="Student">
                    <input id="teacher-button" class="indigoTheme noGap" type="button" form="none" value="Teacher">
                    <input id="admin-button" class="indigoTheme noGap" type="button" form="none" value="Admin">
                </fieldset>
                <!-- Hidden input to store the user type (Triggered by above inputs) -->
                <input type="hidden" id="user-type" name="user-type" name="role" value="Student">

                <!-- General Attributes Fieldset -->
                <fieldset id="generalAttr-fieldset">
                    <!-- First Name -->
                    <div class="input-group">
                        <label for="fname" class="text-label">First Name</label>
                        <input id="fname" class="input-box hover transparent-placeholder" type="text" name="fname"
                            placeholder="First Name" required>
                    </div>
                    <!-- Last Name -->
                    <div class="input-group"> <label for="lname" class="text-label">Last Name</label><input id="lname"
                            class="input-box hover transparent-placeholder" type="text" name="lname"
                            placeholder="Last Name" required>
                    </div>
                    <!-- Email -->
                    <div class="input-group"> <label for="email" class="text-label">Email </label><input id="email"
                            class="input-box hover transparent-placeholder" type="email" name="email"
                            placeholder="Email" required autocomplete="email">
                    </div>
                    <!-- Gender -->
                    <div class="input-group"> <label>Gender</label>
                        <select class="input-box hover transparent-placeholder" name="gender" required>
                            <option> </option>
                            <option value="M"> Male </option>
                            <option value="F"> Female </option>
                        </select>
                    </div>
                    <!-- Date of Birth -->
                    <div class="input-group"> <label>Date of Birth </label><input
                            class="input-box hover transparent-placeholder" type="date" required name="dob">
                    </div>
                    <!-- Password -->
                    <div class="input-group">
                        <lable>Password </lable><input class="input-box hover transparent-placeholder" type="password"
                            required name="password">
                    </div>
                    <!-- Re-enter Password -->
                    <div class="input-group"> <label>Re-enter Password </label><input
                            class="input-box hover transparent-placeholder" type="password" required name="repassword">
                    </div>
                </fieldset>

                <!-- Specific Attributes Fieldset -->
                <fieldset id="specificAttr-fieldset">
                    <!-- Student specific attributes -->
                    <fieldset id="specificAttr-fieldset-student" class="no-border student">
                        <div>
                            <div class="input-group student"> Class Group:
                                <select class="input-box transparent-placeholder hover student" name="classGroup" required>
                                    <option> </option>
                                    <option> Red </option>
                                    <option> Blue </option>
                                </select>
                            </div>
                            <div class="input-group student"> Level :
                                <select class="input-box transparent-placeholder hover " style="padding-left:1px;"
                                    name="level" required>
                                    <option> </option>
                                    <option> 1</option>
                                    <option> 2</option>
                                    <option> 3</option>
                                </select>
                            </div>
                        </div>
                        <div id="subjectWrapper" class="student" name="subjects">
                            <h5> Subjects: </h5>
                            <div id="subjectList">
                                <button id="addSubject-button" class="indigoTheme " form="none">+</button>
                            </div>
                        </div>
                    </fieldset>
                    <!-- Teacher specific attributes -->
                    <fieldset id="specificAttr-fieldset-teacher" class="no-border" style="display:none;">
                        <div class="teacher input-group">
                            Subject Taught: <input class="input-box hover transparent-placeholder " name="subjectTaught"
                                type="text" required>
                        </div>
                        <div class="teacher input-group">
                            Date Joined: <input class="input-box hover transparent-placeholder " name="teacherDateJoined"
                                type="date" required>
                        </div>
                    </fieldset>
                    <fieldset id="specificAttr-fieldset-admin" class="no-border" style="display:none;">
                        <div class="admin input-group">
                            Date Joined <input class="input-box hover transparent-placeholder " name="adminDateJoined"
                                type="date" required>
                        </div>
                    </fieldset>
                </fieldset>

                <!-- Hidden input to store selected subjects -->
                <input type="hidden" id="selected-subjects" name="subjects" value="">

                <!-- Error Message -->
                <span id="errorOutput"> </span>
                <!-- Register button -->
                <button type="submit" name="registrationSubmit-buttonCheck" id="registrationSubmit-button"
                    class="indigoTheme roundBorder" form="registration-form"> Submit</button>
            </form>
        </div>
    </div>

    <!-- Popup window (Default: off) -->
    <?php require 'partials/popUp.php'; ?>

    <script src="scripts/authenticationPage.js" type="module"></script>

</body>

</html>