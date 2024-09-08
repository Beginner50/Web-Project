var CTAStatus = "registration";
var numSubjects = 1;
var userType = "student";
var inTransit = false;

let buttons = ["#student-button", "#teacher-button", "#admin-button"];
let tabs = [$(".student"), $(".teacher"), $(".admin")];

$(".registration").hide();
$(".popUp.window").hide();
$(".teacher").hide();
$(".admin").hide();

$(document).ready(function () {
    // Sliding window effect for the moveable wrapper
    $("button.callToAction").click(function () {
        if (CTAStatus === "registration") {
            $(".registration").slideDown(400);
            $(".login").slideUp(200);
            setTimeout(() => {
                $("#callToAction-wrapper>h1").text("Already Registered?");
                $("#callToAction-wrapper>button").text("Log In");

                $("#form-wrapper>h1").text("Sign Up");
                $("#callToAction-wrapper")
                    .css("gap", "8px");
            }, 500);

            $("#callToAction-wrapper").animate({
                marginLeft: "0%"
            });
            $("#form-wrapper").animate({
                marginLeft: "40%"
            });


            setTimeout(() => {
                CTAStatus = "login";
            }, 1000);
        }
        else {
            $(".registration").slideUp(200);
            $(".login").slideDown(400);
            setTimeout(() => {
                $("#callToAction-wrapper>h1").text("New to Education Portal?");
                $("#callToAction-wrapper>button").text("Sign Up");
                $("#form-wrapper>h1").text("Sign In");

                $("#callToAction-wrapper")
                    .css("gap", "0px");
            }, 500);

            $("#callToAction-wrapper").animate({
                marginLeft: "60%"
            });
            $("#form-wrapper").animate({
                marginLeft: "0%"
            });

            setTimeout(() => {
                CTAStatus = "registration";
            }, 1000);
        }
    })

    // Click events for user type buttons
    for (let index = 0; index < buttons.length; index++) {
        $(buttons[index]).click(function () {
            if (!inTransit) {
                inTransit = true;
                $(buttons[index]).addClass('active');
                $(buttons[(index + 1) % 3]).removeClass('active');
                $(buttons[(index + 2) % 3]).removeClass('active');

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

    $(document).on("click", ".subject img", function () {
        $(this).parent().fadeToggle();
        setTimeout(() => {
            $("div.popUp.window>div").append(
                '<button class="indigoTheme popUp" value="'
                + $(this).parent().text()
                + '">'
                + $(this).parent().text()
                + '</button>'
            );
            $(this).parent().remove();
            --numSubjects;
        }, 100)
    });

    // Click event for add subject button
    $("#addSubject-button").click(function () {
        if (numSubjects < 6) {
            $("div.popUp.window").fadeToggle();
        }
    });

    // Event delegation to handle form options
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
        }, 100)
    });

    $(document).on("click", "div.popUp.window", function () {
        $(this).hide();
    });
});

