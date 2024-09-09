var inTransit = false;

var CTAStatus = "registration";
let buttons = ["#student-button", "#teacher-button", "#admin-button"];
let userTypes = ["Student", "Teacher", "Admin"];
let tabs = [$(".student"), $(".teacher"), $(".admin")];

// Max num subjects = 5
var numSubjects = 1;
$(document).ready(function () {
    // AJAX to fetch list of subjects from the database
    $.ajax({
        url: "php/getSubjects.php",
        method: "GET",
        dataType: "json",
        success: function (data) {
            data.forEach(function (elem) {
                addPopupButton(elem);
            });
        },
        error: function (xhr, status, error) {
            console.log(xhr);
            $("#errorOutput").text(status + " " + error);
        }
    });

    // Sliding window effect for the moveable wrapper
    $("button.callToAction").click(function () {
        if (inTransit)
            return;
        if (CTAStatus === "registration") {
            inTransit = true;
            $(".registration").slideDown(400);
            $(".login").slideUp(200);

            setTimeout(() => {
                $("#callToAction-wrapper>h1").text("Already Registered?");
                $("#callToAction-wrapper>button>a").text("Log In");

                $("#form-wrapper>h1").text("Sign Up");
            }, 500);

            $("#callToAction-wrapper").animate({
                marginLeft: "0%"
            });
            $("#form-wrapper").animate({
                marginLeft: "40%"
            });

            inTransit = false;
            CTAStatus = "login";
        }
        else {
            inTransit = true;
            $(".registration").slideUp(200);
            $(".login").slideDown(400);
            setTimeout(() => {
                $("#callToAction-wrapper>h1").text("New to Education Portal?");
                $("#callToAction-wrapper>button>a").text("Sign Up");
                $("#form-wrapper>h1").text("Sign In");
            }, 500);

            $("#callToAction-wrapper").animate({
                marginLeft: "60%"
            });
            $("#form-wrapper").animate({
                marginLeft: "0%"
            });

            inTransit = false;
            CTAStatus = "registration";
        }
    });

    // Click events for user type buttons
    for (let index = 0; index < buttons.length; index++) {
        $(buttons[index]).click(function () {
            if (!inTransit) {
                inTransit = true;
                $(buttons[index]).addClass('active');
                $(buttons[(index + 1) % 3]).removeClass('active');
                $(buttons[(index + 2) % 3]).removeClass('active');

                $("#user-type").val(userTypes[index]);

                tabs[(index + 1) % tabs.length].fadeOut(200);
                tabs[(index + 2) % tabs.length].fadeOut(200);
                setTimeout(function () {
                    tabs[index % tabs.length].fadeIn(10);
                    inTransit = false;
                }, 300);
            }
        });
    }

    // Event delegation to handle click and hover events on subject close icon
    $(document).on("mouseenter", ".subject img", function () {
        $(this).attr('src', 'icons/backspaceRed.svg');
        $(this).parent().css('color', 'red');
        $(this).parent().css('border-color', 'red');
    });
    $(document).on("mouseleave", ".subject img", function () {
        $(this).attr('src', 'icons/backspace.svg');
        $(this).parent().css('color', 'var(--purpleVortex)');
        $(this).parent().css('border-color', 'var(--purpleVortex)');
    });

    // Function to add buttons in popup menu
    function addPopupButton(value) {
        $("div.popUp.window>div").append(
            '<button class="indigoTheme popUp" value="'
            + value + '">'
            + value
            + '</button>'
        );
    }

    // Click event on subject deletion image
    $(document).on("click", ".subject img", function () {
        $(this).parent().fadeToggle();
        setTimeout(() => {
            addPopupButton($(this).parent().text());
            $(this).parent().remove();
            --numSubjects;
        }, 100);
    });

    // Click event for add subject button
    $("#addSubject-button").click(function () {
        if (numSubjects < 6) {
            $("div.popUp.window").fadeToggle();
        }
    });

    // Event delegation to handle popup buttons
    $(document).on("click", "button.popUp", function () {
        $("addSubject-button").detach();
        setTimeout(() => {
            $("#subjectList").append(
                '<div class="subject">'
                + $(this).attr("value")
                + '<img src="icons/backspace.svg"></img>'
                + '</div>'
            );
            $(this).remove();
            $("#addSubject-button").appendTo($("#subjectList"));
            ++numSubjects;
        }, 100);
    });

    $(document).on("click", "div.popUp.window", function () {
        $(this).hide();
    });

    // Collect selected subjects and store in hidden input before form submission
    $("#registration-form").submit(function () {
        let subjectsArray = [];

        // Collect all selected subjects from #subjectList
        $("#subjectList .subject").each(function () {
            subjectsArray.push($(this).text().trim()); // Get the subject text
        });

        // Store subjects in a hidden input field
        $("#selected-subjects").val(subjectsArray.join(",")); // Join them as a comma-separated string
    });
});

