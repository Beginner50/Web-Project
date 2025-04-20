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

            // login redirects to page controller, which gets resources, saves session & navigate to page
            $.ajax({
                url: "/login",
                type: "POST",
                data: formData,
                success: function(response) {
                    const userID = response["userID"];
                    const userType = response["userType"];
                    window.location.href = "/account/" + userType + "/" + userID;
                },
                error: function(xhr, status, error) {
                    if (xhr.status == 409 || xhr.status == 401) {

                        const response = xhr.responseJSON;
                        alert("Error:\n" + response.errors.join('\n'));
                    } else
                        alert("An error occurred while submitting the form.");
                }
            });
        });
    });
</script>