<form id="login-form" method="post" action="/login">
    <div class="input-group"> Email: <input class="input-box" type="email" name="email" required
            autocomplete="email">
    </div>
    <div class="input-group"> Password: <input class="input-box" type="password" name="password" id="login-password" required>
    </div>
    <button id="loginSubmit-button" type="submit" class="indigoTheme roundBorder" form="login-form">
        Submit
    </button>
</form>


<!-- Tasks -->
<!-- Re-write submission logic and instead use AJAX -->
<!-- Make display error page into its own url -->

<!-- $errors = [];

if ($page == "registration")
$errors = require 'models/Authentication/registration.php';
else if ($page == "login")
$errors = require 'models/Authentication/login.php';

// Display errors (if any), or redirect to account page
if ($errors)
require 'views/Authentication/authenticationErrorView.php';
else
header('Location: /account');
break; -->