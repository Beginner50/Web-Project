<form id="registration-form" method="post" action="controllers/authenticationController.php"
    style="display: none;">
    <!-- User Type Fieldset (Select between different users) -->
    <fieldset id="userType-fieldset">
        <input id="student-button" class="indigoTheme active noGap" type="button" form="none"
            value="Student">
        <input id="teacher-button" class="indigoTheme noGap" type="button" form="none" value="Teacher">
        <input id="admin-button" class="indigoTheme noGap" type="button" form="none" value="Admin">
    </fieldset>
    <!-- Hidden input to store the user type (Triggered by above inputs) -->
    <input type="hidden" id="user-type" name="user-type" name="role" value="Student">

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
            <select class="input-box hover transparent-placeholder" name="gender" required>
                <option> </option>
                <option value="M"> Male </option>
                <option value="F"> Female </option>
            </select>
        </div>
        <!-- Date of Birth -->
        <div class="input-group"> <label>Date of Birth </label><input
                class="input-box hover transparent-placeholder" type="date" required name="dob">
        </div>
        <!-- Password -->
        <div class="input-group">
            <label>Password </label><input class="input-box hover transparent-placeholder" type="password"
                required name="password" pattern="(?=.*[A-Z])(?=.*\d).{5,}" minlength="5">
        </div>
        <!-- Re-enter Password -->
        <div class="input-group"> <label>Re-enter Password </label><input
                class="input-box hover transparent-placeholder" type="password" required name="repassword"
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
                    <select class="input-box transparent-placeholder hover student" name="classGroup" required>
                        <option> </option>
                        <option> Red </option>
                        <option> Blue </option>
                    </select>
                </div>
                <div class="input-group student">
                    <span> Level:</span>
                    <select class="input-box transparent-placeholder hover " style="padding-left:1px;"
                        name="level" required>
                        <option> </option>
                        <option> 1</option>
                        <option> 2</option>
                        <option> 3</option>
                    </select>
                </div>
            </div>
            <div id="bottom-section">
                <h5 id="subjectList-header"> Subjects: </h5>
                <div id="subjectList">
                    <button id="addSubject-button" class="indigoTheme " form="none">+</button>
                </div>
            </div>
        </fieldset>
        <!-- Teacher specific attributes -->
        <fieldset id="specificAttr-fieldset-teacher" class="no-border" style="display:none;" disabled>
            <div class="teacher input-group">
                Subject Taught: <input class="input-box hover transparent-placeholder " name="subjectTaught"
                    type="text" required>
            </div>
            <div class="teacher input-group">
                Date Joined: <input class="input-box hover transparent-placeholder " name="teacherDateJoined"
                    type="date" required>
            </div>
        </fieldset>
        <fieldset id="specificAttr-fieldset-admin" class="no-border" style="display:none;" disabled>
            <div class="admin input-group">
                Date Joined <input class="input-box hover transparent-placeholder " name="adminDateJoined"
                    type="date" required>
            </div>
        </fieldset>
    </fieldset>

    <!-- Register button -->
    <button type="submit" id="registrationSubmit-button"
        class="indigoTheme roundBorder" form="registration-form"> Submit</button>

    <!-- Hidden input to store selected subjects from modal -->
    <input type="hidden" id="selected-subjects" name="subjects" value="[]">

    <input type="hidden" name="form_type" value="registration">
</form>

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

    document.addEventListener("DOMContentLoaded", () => {
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
            });
        })
    });
</script>

<!-- Subject List Logic -->
<script>
    let addSubjectButton = document.getElementById('addSubject-button');
    const selectedSubjectsInput = document.getElementById("selected-subjects");
    const subjectList = document.getElementById("subjectList");

    function createSubjectListEntry(subjectCode) {
        const subjectListEntry = document.createElement("div");
        subjectListEntry.className = "subject";
        const subjectListEntryIcon = document.createElement("img");
        subjectListEntryIcon.src = "assets/backspace.svg"
        subjectListEntry.textContent = subjectCode;
        subjectListEntry.appendChild(subjectListEntryIcon);

        return subjectListEntry;
    }

    function deleteSubjectListEntry(subjectListEntry) {
        subjectListEntry.remove();
    }

    sharedState.onSubjectSelect = function addSubjectListEntry(subjectCode) {
        // Creates a subject list entry & adds deselect functionality to it
        let subjectListEntry = createSubjectListEntry(subjectCode);
        deselectSubjectEntryListFunctionality(subjectListEntry);

        // Places addSubject button ahead of last subject list entry
        addSubjectButton = subjectList.removeChild(subjectList.querySelector("button"));
        subjectList.appendChild(subjectListEntry);
        subjectList.appendChild(addSubjectButton);

        // Increment the selected subjects count
        sharedState.selectedSubjects++;
    }

    function removeSubjectListEntry(subjectListEntry) {
        // Delete the subject list entry from the subjectList
        deleteSubjectListEntry(subjectListEntry);

        // Decrement the selected subjects count
        --sharedState.selectedSubjects;

        // Call the modal to show the corresponding subject entry
        sharedState.onSubjectDeselect(subjectListEntry.textContent);
    }

    function deselectSubjectEntryListFunctionality(subjectListEntry) {
        let subjectListEntryIcon = subjectListEntry.querySelector("img");

        // CSS applies only to elements before the DOM has been loaded.
        // Therefore, event listeners are required here
        subjectListEntryIcon.addEventListener("mouseenter", event => {
            event.target.src = "assets/backspaceRed.svg";
            event.target.parentElement.style.color = "red";
            event.target.parentElement.style.borderColor = "red";
        });
        subjectListEntryIcon.addEventListener("mouseleave", event => {
            event.target.src = "assets/backspace.svg";
            event.target.parentElement.style.color = "var(--purpleVortex)";
            event.target.parentElement.style.borderColor = "var(--purpleVortex)";
        });

        /*
          If user clicks on icon, delete the corresponding subject entry
          from the subject list
        */
        subjectListEntryIcon.addEventListener("mousedown", event => {
            removeSubjectListEntry(subjectListEntryIcon.parentElement);
        });
    }
</script>