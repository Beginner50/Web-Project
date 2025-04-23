<link rel="stylesheet" href="stylesheets/authentication/subjectList.css">

<div id="bottom-section">
    <dialog id="subjectDialog">
        <h4>Select a subject</h4>
        <ul id="courseList" class="list-group"></ul>
        <button id="closeDialogButton" class="btn btn-secondary mt-2" type="button">Close</button>
    </dialog>

    <div id="subjectList">
        <button id="addSubject-button" class="indigoTheme" type="button"> + </button>
    </div>
</div>


<script>
    $(document).ready(function() {
        let courses = [];

        $.ajax({
            url: "/subjects",
            method: "GET",
            dataType: "json",
            success: function(response) {
                courses = response["data"].map(subject => {
                    return {
                        [subject.SubjectCode]: subject.SubjectName
                    };
                });
            },
            error: function(xhr, status, error) {}
        });

        let selectedSubjects = [];
        const maxSubjects = 5;
        const subjectDialog = $("#subjectDialog")[0];

        // Attach event listeners
        $(document).on("click", "#addSubject-button", openSubjectModal);
        $("#closeDialogButton").on("click", closeSubjectModal);
        $(document).on("click", ".remove-subject", removeSubject);

        function openSubjectModal() {
            if (selectedSubjects.length < maxSubjects) {
                populateModalCourseList();
                subjectDialog.showModal();
            }
            document.body.style.overflow = 'hidden';
        }

        function closeSubjectModal() {
            subjectDialog.close();
            document.body.style.overflow = '';
        }

        function populateModalCourseList() {
            $("#courseList").empty();

            courses
                .filter(course => !selectedSubjects.find(selectedSubject => selectedSubject == Object.entries(course)[0][0]))
                .forEach((courseObj, index) => {
                    const courseId = Object.keys(courseObj)[0];
                    const courseName = courseObj[courseId];

                    const listItem = $("<li>")
                        .addClass("list-group-item d-flex justify-content-between align-items-center")
                        .text(courseName);

                    const selectButton = $("<button type='button'>")
                        .addClass("btn btn-primary btn-sm")
                        .text("Select")
                        .on("click", function() {
                            selectCourse(courseId, index);
                        });

                    listItem.append(selectButton);
                    $("#courseList").append(listItem);
                });
        }

        function selectCourse(courseId, index) {
            if (selectedSubjects.length < maxSubjects) {
                addSubjectToList(courseId);
                selectedSubjects.push(courseId);
                populateModalCourseList();

                $("#selected-subjects").val(selectedSubjects);
            }

            if (selectedSubjects.length === maxSubjects) {
                $("#addSubject-button").hide();
                closeSubjectModal();
            }
        }

        function addSubjectToList(courseId) {
            const subjectEntry = $("<div>").addClass("subject").text(courseId);
            const subjectEntryIcon = $("<img>")
                .attr("src", "assets/backspace.svg")
                .addClass("remove-subject")
                .on({
                    mouseenter: function() {
                        $(this).attr("src", "assets/backspaceRed.svg");
                        $(this).parent().css({
                            color: "red",
                            borderColor: "red"
                        });
                    },
                    mouseleave: function() {
                        $(this).attr("src", "assets/backspace.svg");
                        $(this).parent().css({
                            color: "var(--purpleVortex)",
                            borderColor: "var(--purpleVortex)"
                        });
                    }
                });

            subjectEntry.append(subjectEntryIcon);
            $("#subjectList").append(subjectEntry);
            $("#addSubject-button").appendTo("#subjectList");
        }

        function removeSubject() {
            const subjectEntry = $(this).parent();
            const courseId = subjectEntry.text();

            subjectEntry.remove();
            selectedSubjects = selectedSubjects.filter(subject => subject !== courseId);

            populateModalCourseList();

            if (selectedSubjects.length < maxSubjects) {
                $("#addSubject-button").show();
            }
        }

        function getCourseName(courseId) {
            const course = courses.find(courseObj => Object.keys(courseObj)[0] === courseId);
            return course ? course[courseId] : "";
        }
    });
</script>