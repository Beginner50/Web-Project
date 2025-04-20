<form id="login-form" action="/login" method="post">
    <div class="input-group"> Email: <input id="login-email" class="input-box" type="email" name="email" required
            autocomplete="email">
    </div>
    <div class="input-group"> Password: <input id="login-password" class="input-box" type="" name="password" id="login-password" required>
    </div>
    <button id="loginSubmit-button" type="submit" class="indigoTheme roundBorder" form="login-form">
        Submit
    </button>
</form>

<script>
    $(document).ready(function() {
        $("#login-email").val(window.localStorage.getItem("login-email"));
        $("#login-password").val(window.localStorage.getItem("login-password"));

        $("#login-form").on("submit", function(event) {
            const formData = new FormData(this);
            window.localStorage.setItem("login-email", formData.get("email"));
            window.localStorage.setItem("login-password", formData.get("password"));
        });
    });
</script>