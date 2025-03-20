<form id="login-form">
    <div class="input-group"> Email: <input class="input-box" type="email" name="email" required
            autocomplete="email">
    </div>
    <div class="input-group"> Password: <input class="input-box" type="" name="password" id="login-password" required>
    </div>
    <button id="loginSubmit-button" type="submit" class="indigoTheme roundBorder" form="login-form">
        Submit
    </button>
</form>

<!---------------------------------------------- Javascript --------------------------------------------->
<!-- Login Form Submission Logic -->
<script>
    $(document).ready(function() {
        $("#login-form").on("submit", function(event) {
            event.preventDefault();

            const formData = $(this).serialize();

            $.ajax({
                url: "/login",
                type: "POST",
                data: formData,
                success: function(response) {
                    window.location.href = "/account";
                },
                error: function(xhr, status, error) {
                    if (xhr.status == 409)
                        alert(JSON.parse(xhr.responseText)[0]);
                    else
                        alert("An error occurred while submitting the form.");
                }
            });
        });
    });
</script>