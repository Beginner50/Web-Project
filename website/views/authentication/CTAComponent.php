<!-- Call To Action Markup -->
<h1 class="callToAction">
    New to Education
    Portal?</h1>
<button class="callToAction" href="#main-wrapper"> <a>Sign Up</a></button>

<!-- Call To Action Logic -->
<script>
    $(document).ready(function() {
        const CTAWrapper = document.getElementById("callToAction-wrapper");
        const formWrapper = document.getElementById("form-wrapper");

        const CTAH1 = $("#callToAction-wrapper h1");
        const CTAButton = $("#callToAction-wrapper button");
        const CTAButtonLink = $("#callToAction-wrapper button a");
        const formWrapperH1 = $("#form-wrapper h1");
        const loginForm = $("#login-form");
        const registrationForm = $("#registration-form");

        let formStatus = "login";

        const CTAkeyframesLeft = [{
                transform: "translateX(0%)"
            },
            {
                transform: "translateX(-150%)"
            }
        ];
        const CTAkeyframesRight = [{
                transform: "translateX(-150%)"
            },
            {
                transform: "translateX(0%)"
            }
        ];
        const formkeyframesRight = [{
                transform: "translate(0%)",
                opacity: "1"
            },
            {
                transform: "translateX(5%)",
                opacity: "0"
            },
            {
                transform: "translateX(55%)",
                opacity: "0"
            },
            {
                transform: "translateX(65%)"
            }
        ];
        const formkeyframesLeft = [{
                transform: "translateX(65%)",
                opacity: "1"
            },
            {
                transform: "translateX(65%)",
                opacity: "0"
            },
            {
                transform: "translateX(10%)",
                opacity: "0"
            },
            {
                transform: "translateX(0%)"
            }
        ];
        const options = {
            duration: 600,
            easing: "ease-in-out",
            fill: "forwards"
        };

        CTAButton.on("mousedown", function() {
            if (formStatus === "login") {
                // Fade out animation for login form
                setTimeout(() => {
                    loginForm.css("display: none;");
                    registrationForm.css("display: flex;");
                }, 250);

                // Translate the forms
                formWrapper.animate(formkeyframesRight, options);
                CTAWrapper.animate(CTAkeyframesLeft, options);

                // Fade in animation for registration form
                setTimeout(function() {
                    loginForm.css("display", "none");
                    registrationForm.css("display", "flex");
                }, 250);

                CTAH1.text("Already Registered?");
                CTAButtonLink.text("Sign In");
                formWrapperH1.text("Sign Up");
                formStatus = "registration";
            } else {
                // Fade out animation for registration form
                setTimeout(() => {
                    loginForm.css("display: flex;");
                    registrationForm.css("display: none");
                }, 250);

                // Translate the forms
                formWrapper.animate(formkeyframesLeft, options);
                CTAWrapper.animate(CTAkeyframesRight, options);

                // Fade in animation for the login form
                setTimeout(function() {
                    loginForm.css("display", "flex");
                    registrationForm.css("display", "none");
                }, 250);

                CTAH1.text("New to Education Portal?");
                CTAButtonLink.text("Sign Up");
                formWrapperH1.text("Sign In");
                formStatus = "login";
            }
        });
    });
</script>