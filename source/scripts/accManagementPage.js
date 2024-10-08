function togglePasswordVisibility(passwordId, toggleId) {

    var passwordInput = document.getElementById(passwordId);
    var toggleIcon = document.getElementById(toggleId);

    if (passwordInput.type === "password") { //displaying dots
        passwordInput.type = "text";
        toggleIcon.src = "assets/eye-open.png"; //  icon to indicate hiding

    } else { //displaying texts
        passwordInput.type = "password";
        toggleIcon.src = "assets/eye-close.png"; //  show icon
    }
}
