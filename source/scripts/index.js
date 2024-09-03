var CTAStatus = "registration";
var numSubjects = 1;
var userType = "student";

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
                $("#CTAMoveableWrapper>h").text("Already Registered?");
                $("#CTAMoveableWrapper>button").text("Log In");

                $("#formMoveableWrapper>h1").text("Sign Up");
                $("#CTAMoveableWrapper")
                    .css("gap", "8px");
            }, 500);

            $("#CTAMoveableWrapper").animate({
                marginLeft: "0%"
            });
            $("#formMoveableWrapper").animate({
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
                $("#CTAMoveableWrapper>h").text("New to Education Portal?");
                $("#CTAMoveableWrapper>button").text("Sign Up");
                $("#formMoveableWrapper>h1").text("Sign In");

                $("#CTAMoveableWrapper")
                    .css("gap", "0px");
            }, 500);

            $("#CTAMoveableWrapper").animate({
                marginLeft: "60%"
            });
            $("#formMoveableWrapper").animate({
                marginLeft: "0%"
            });

            setTimeout(() => {
                CTAStatus = "registration";
            }, 1000);
        }
    })

    // Click events for user type buttons
    $("#studentButton").click(function () {
        $("#studentButton").addClass('active');
        $("#adminButton").removeClass('active');
        $("#teacherButton").removeClass('active');

        $("#teacherButton").attr("disabled", "disabled");
        $("#adminButton").attr("disabled", "disabled");

        $(".teacher").fadeOut(100);
        $(".admin").fadeOut(100);
        setTimeout(function () {
            $(".student").fadeIn();
            $("#teacherButton").removeAttr("disabled");
            $("#adminButton").removeAttr("disabled");
        }, 200);

    });
    $("#teacherButton").click(function () {
        $("#studentButton").removeClass('active');
        $("#adminButton").removeClass('active');
        $("#teacherButton").addClass('active');

        $("#studentButton").attr("disabled", "disabled");
        $("#adminButton").attr("disabled", "disabled");

        $(".student").fadeOut(100);
        $(".admin").fadeOut(100);
        setTimeout(function () {
            $(".teacher").fadeIn();
            $("#studentButton").removeAttr("disabled");
            $("#adminButton").removeAttr("disabled");
        }, 200);
    });
    $("#adminButton").click(function () {
        $("#studentButton").removeClass('active');
        $("#adminButton").addClass('active');
        $("#teacherButton").removeClass('active');

        $("#studentButton").attr("disabled", "disabled");
        $("#teacherButton").attr("disabled", "disabled");

        $(".teacher").fadeOut(100);
        $(".student").fadeOut(100);
        setTimeout(function () {
            $(".admin").fadeIn();
            $("#studentButton").removeAttr("disabled");
            $("#teacherButton").removeAttr("disabled");
        }, 200);
    });


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
    $("#addSubjectButton").click(function () {
        if (numSubjects < 6) {
            $("div.popUp.window").fadeToggle();
        }
    });

    // Event delegation to handle form options
    $(document).on("click", "button.popUp", function () {
        $("addSubjectButton").detach();
        setTimeout(() => {
            $("#subjectList").append(
                '<div class="subject">'
                + $(this).attr("value")
                + '<img src="icons/backspace.svg"></img>'
                + '</div>'
            );
            $(this).remove();
            $("#addSubjectButton").appendTo($("#subjectList"));
            ++numSubjects;
        }, 100)
    });

    $(document).on("click", "div.popUp.window", function () {
        $(this).hide();
    });
});

