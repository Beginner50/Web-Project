<!-- AUTHENTICATION VIEW MARKUP -->
<div id="main-wrapper" class="roundBorder-15">
    <!-- Call To Action Wrapper (Switches between Login & Registration)-->
    <div id="callToAction-wrapper" class="registrationCTA">
        <h1 class="callToAction">
            New to Education
            Portal?</h1>
        <button class="callToAction" href="#main-wrapper"> <a>Sign Up</a></button>
    </div>

    <!-- Form Wrapper -->
    <div id="form-wrapper">
        <h1>Sign In</h1>

        <!-- Login Form (Default: Visible) -->
        <?php require 'partials/loginForm.php' ?>

        <!-- Registration Form (Default: Hidden)-->
        <?php require 'partials/registrationForm.php' ?>
    </div>
</div>

<!-- Call To Action Logic -->
<script>
    const CTAWrapper = document.querySelector("#callToAction-wrapper");
    const CTAH1 = CTAWrapper.querySelector("h1");
    const CTAButton = document.querySelector("#callToAction-wrapper>button");
    const CTAButtonLink = CTAButton.querySelector("a");

    const formWrapper = document.querySelector("#form-wrapper");
    const formWrapperH1 = formWrapper.querySelector("h1");

    const loginForm = document.getElementById("login-form");
    const registrationForm = document.getElementById("registration-form");

    let formStatus = "login";
    let inTransit = false;

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
            opacity: "100"
        },
        {
            transform: "translate(5%)",
            opacity: "0"
        },
        {
            transform: "translate(55%)",
            opacity: "0"
        },
        {
            transform: "translate(65%)"
        }
    ];
    const formkeyframesLeft = [{
            transform: "translate(65%)",
            opacity: "100"
        },
        {
            transform: "translate(65%)",
            opacity: "0"
        },
        {
            transform: "translate(10%)",
            opacity: "0"
        },
        {
            transform: "translate(0%)"
        }
    ];
    const options = {
        duration: 600,
        easing: "ease-in-out",
        fill: "forwards"
    };

    function getNewFormStatus() {
        if (formStatus == "login")
            formStatus = "registration";
        else if (formStatus == "registration")
            formStatus = "login"

        return formStatus;
    }

    function showLoginForm() {
        setTimeout(() => {
            loginForm.style.display = "";
            registrationForm.style.display = "none";
            inTransit = false;
        }, 250);
        formWrapper.animate(formkeyframesLeft, options);
        CTAWrapper.animate(CTAkeyframesRight, options);

        CTAH1.textContent = "New to Education Portal?";
        CTAButtonLink.textContent = "Sign Up";
        formWrapperH1.textContent = "Sign In";
    }

    function showRegistrationForm() {
        setTimeout(() => {
            loginForm.style.display = "none";
            registrationForm.style.display = "";
            inTransit = false;
        }, 250);
        formWrapper.animate(formkeyframesRight, options);
        CTAWrapper.animate(CTAkeyframesLeft, options);

        CTAH1.textContent = "Already Registered?";
        CTAButtonLink.textContent = "Sign In";
        formWrapperH1.textContent = "Sign Up";
    }

    function updateFormView(newFormStatus) {
        if (inTransit)
            return;

        inTransit = true;
        if (newFormStatus === "login")
            showLoginForm();
        else if (newFormStatus === "registration")
            showRegistrationForm();
    }

    document.addEventListener("DOMContentLoaded", () => {
        /*
          Upon clicking Sign In/Up button, changes and gets new form status from model
          and updates the state of the form in the view
        */
        CTAButton.addEventListener("mousedown", () => {
            let newformStatus = getNewFormStatus();
            updateFormView(newformStatus);
        });
    });
</script>