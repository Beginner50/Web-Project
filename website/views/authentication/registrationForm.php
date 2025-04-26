<form id="registration-form" style="display:none;" action="/authentication?action=registration" method="post">
    <!-- User Type Fieldset (Select between different users) -->
    <fieldset id="userType-fieldset">
        <button id="student-button" class="indigoTheme active noGap" type="button" form="none"
            value="student"> Student </button>
        <button id="teacher-button" class="indigoTheme noGap" type="button" form="none" value="teacher"> Teacher </button>
        <button id="admin-button" class="indigoTheme noGap" type="button" form="none" value="admin"> Admin </button>
    </fieldset>

    <!-- Hidden input to store the user type (Triggered by above inputs) -->
    <input type="hidden" id="user-type" name="user-type" name="role" value="student">

    <!-- General Attributes Fieldset -->
    <fieldset id="generalAttr-fieldset">
        <!-- First Name -->
        <div class="input-group">
            <label for="fname" class="text-label">First Name</label>
            <input id="fname" class="input-box hover transparent-placeholder" type="text" name="fname"
                placeholder="First Name" required>
        </div>
        <!-- Last Name -->
        <div class="input-group"> <label for="lname" class="text-label">Last Name</label><input id="lname"
                class="input-box hover transparent-placeholder" type="text" name="lname"
                placeholder="Last Name" required>
        </div>
        <!-- Email -->
        <div class="input-group"> <label for="email" class="text-label">Email </label><input id="email"
                class="input-box hover transparent-placeholder" type="email" name="email"
                placeholder="Email" required autocomplete="email">
        </div>
        <!-- Gender -->
        <div class="input-group"> <label>Gender</label>
            <select id="gender" class="input-box hover transparent-placeholder" name="gender" required>
                <option> </option>
                <option value="M"> Male </option>
                <option value="F"> Female </option>
            </select>
        </div>
        <!-- Date of Birth -->
        <div class="input-group"> <label>Date of Birth </label><input
                id="dob" class="input-box hover transparent-placeholder" type="date" required name="dob">
        </div>
        <!-- Password -->
        <div class="input-group">
            <label>Password </label><input id="password" class="input-box hover transparent-placeholder" type="password"
                required name="password" pattern="(?=.*[A-Z])(?=.*\d).{5,}" minlength="5">
        </div>
        <!-- Re-enter Password -->
        <div class="input-group"> <label>Re-enter Password </label><input id="repeat-password"
                class="input-box hover transparent-placeholder" type="password" required name="repeat-password"
                pattern="(?=.*[A-Z])(?=.*\d).{5,}" minlength="5">
        </div>
    </fieldset>

    <!-- Specific Attributes Fieldset -->
    <fieldset id="specificAttr-fieldset">
        <!-- Student specific attributes -->
        <fieldset id="specificAttr-fieldset-student" class="no-border student">
            <div id="top-section">
                <div class="input-group student">
                    <span> Class Group: </span>
                    <select id="class-group" class="input-box transparent-placeholder hover student" name="class-group" required>
                        <option> </option>
                        <option> Red </option>
                        <option> Blue </option>
                    </select>
                </div>
                <div class="input-group student">
                    <span> Level:</span>
                    <select id="level" class="input-box transparent-placeholder hover " style="padding-left:1px;"
                        name="level" required>
                        <option> </option>
                        <option> 1</option>
                        <option> 2</option>
                        <option> 3</option>
                    </select>
                </div>
            </div>
            <div id="bottom-section">
                <div id="subject-list">
                    <button id="addSubject-button" class="indigoTheme" type="button"> + </button>
                </div>
            </div>
        </fieldset>

        <input id="date-joined" class="input-box hover transparent-placeholder " name="date-joined"
            type="date" required hidden>

        <!-- Teacher specific attributes -->
        <fieldset id="specificAttr-fieldset-teacher" class="no-border" style="display:none;" disabled>
            <div class="teacher input-group">
                Subject Taught:
                <select id="subject-taught" class="input-box hover transparent-placeholder " name="subject-taught" required>
                </select>
            </div>
            <div class="teacher input-group">
                <label for="date-joined"> Date Joined </label>
            </div>
        </fieldset>
        <fieldset id="specificAttr-fieldset-admin" class="no-border" style="display:none;" disabled>
            <div class="admin input-group">
                <label for="date-joined"> Date Joined </label>
            </div>
        </fieldset>
    </fieldset>

    <!-- Register button -->
    <button type="submit" id="registrationSubmit-button"
        class="indigoTheme roundBorder" form="registration-form"> Submit</button>

    <!-- Hidden input to store selected subjects from modal -->
    <input type="hidden" id="selected-subjects" name="subjects" value="[]">
</form>

<!---------------------------------------------- Javascript --------------------------------------------->
<!-- User Type Selection Logic -->
<script>
    inTransit = false;
    const userTypeButtons = [
        document.getElementById("student-button"),
        document.getElementById("teacher-button"),
        document.getElementById("admin-button")
    ];
    const userFieldsets = [
        document.querySelector("#specificAttr-fieldset-student"),
        document.querySelector("#specificAttr-fieldset-teacher"),
        document.querySelector("#specificAttr-fieldset-admin")
    ];

    function updateUserTab(currentTab) {
        if (inTransit)
            return;
        inTransit = true;

        userTypeButtons[currentTab].classList.add("active");
        userTypeButtons[(currentTab + 1) % 3].classList.remove("active");
        userTypeButtons[(currentTab + 2) % 3].classList.remove("active");

        setTimeout(() => {
            userFieldsets[currentTab].style.display = "";
            userFieldsets[currentTab].disabled = false;
        }, 200)
        userFieldsets[(currentTab + 1) % 3].style.display = "none";
        userFieldsets[(currentTab + 1) % 3].disabled = true;
        userFieldsets[(currentTab + 2) % 3].style.display = "none";
        userFieldsets[(currentTab + 2) % 3].disabled = true;

        inTransit = false;
    }

    function updateUserTypeInput(userType) {
        document.getElementById("user-type").value = userType;
    }

    function showRoleFields(role) {
        $(`#specificAttr-fieldset-${role}`).show().prop("disabled", false);

        // Find the correct input-group div, and append #date-joined into it
        let targetGroup = $(`#specificAttr-fieldset-${role} .input-group:has(label[for='date-joined'])`);
        targetGroup.append($("#date-joined").prop("hidden", false));
    }


    $(document).ready(() => {
        /*
          When user clicks on a userType button, get the index of the button clicked and update
          the user specific fieldset of the registration form accordingly.
        */
        userTypeButtons.forEach(button => {
            button.addEventListener("mousedown", () => {
                let currentTab = userTypeButtons.findIndex(cmp => {
                    return cmp === button;
                });
                updateUserTab(currentTab);
                updateUserTypeInput(button.value);
                if (button.value == "admin" || button.value == "teacher")
                    showRoleFields(button.value);
            });
        });


    });
</script>

<script>
    $(document).ready(function() {
        // Populate subject dropdown
        $.ajax({
            url: "http://localhost/subjects?limit=999",
            "method": "GET",
            "dataType": "json",
            "success": function(response) {
                if (response.success && response.data)
                    response.data.forEach(function(subject) {
                        $("#subject-taught").append(
                            `<option value=${subject.SubjectCode}> ${subject.SubjectName} </option>`
                        );
                    });
            }
        })

        // Cookie Logic
        $("#fname").val(window.localStorage.getItem("fname"));
        $("#lname").val(window.localStorage.getItem("lname"));
        $("#email").val(window.localStorage.getItem("email"));
        $("#gender").val(window.localStorage.getItem("gender"));
        $("#dob").val(window.localStorage.getItem("dob"));
        $("#password").val(window.localStorage.getItem("password"));
        $("#repeat-password").val(window.localStorage.getItem("repeat-password"));

        $("#class-group").val(window.localStorage.getItem("class-group"));
        $("#level").val(window.localStorage.getItem("level"));
        $("#teacher-date-joined").val(window.localStorage.getItem("teacher-date-joined"));
        $("#subject-taught").val(window.localStorage.getItem("subject-taught"));
        $("#admin-date-joined").val(window.localStorage.getItem("admin-date-joined"));


        $("#registration-form").submit(function(e) {
            const formData = new FormData(this);

            window.localStorage.setItem("fname", formData.get("fname"));
            window.localStorage.setItem("lname", formData.get("lname"));
            window.localStorage.setItem("email", formData.get("email"));
            window.localStorage.setItem("gender", formData.get("gender"));
            window.localStorage.setItem("dob", formData.get("dob"));
            window.localStorage.setItem("password", formData.get("password"));
            window.localStorage.setItem("repeat-password", formData.get("repeat-password"));

            window.localStorage.setItem("class-group", formData.get("class-group"));
            window.localStorage.setItem("level", formData.get("level"));
            window.localStorage.setItem("teacher-date-joined", formData.get("teacher-date-joined"));
            window.localStorage.setItem("subject-taught", formData.get("subject-taught"));
            window.localStorage.setItem("admin-date-joined", formData.get("admin-date-joined"));
        });

        /*-------------------- Event listener on subject entries -----------------------*/
        // Open subject modal upon clicking add-subject button
        $("#subject-list").on("click", "#addSubject-button", function() {
            openSubjectModal();
        });

        // Animate subject list entry on hover
        $("#subject-list").on("mouseenter mouseleave", ".subject > img", function(event) {
            const isEnter = event.type === "mouseenter";
            $(this).attr("src", isEnter ? "assets/backspaceRed.svg" : "assets/backspace.svg");
            $(this).parent().css({
                color: isEnter ? "red" : "var(--purpleVortex)",
                borderColor: isEnter ? "red" : "var(--purpleVortex)"
            });
        });

        // Remove the subject list entry upon clicking delete icon
        $("#subject-list").on("click", ".subject > img", function(event) {
            // Remove selected subject
            let selectedSubjects = JSON.parse($('#selected-subjects').val());
            selectedSubjects = selectedSubjects.filter((elem) => {
                if (elem == $(this).parent().data('id'))
                    return false;
                return true;
            });
            $('#selected-subjects').val(JSON.stringify(selectedSubjects));

            // Reveal add button if selected subjects < 5
            if (selectedSubjects.length < 5)
                $('#addSubject-button').show();

            // Destroy element
            $(this).parent().remove();
        });
    });
</script>