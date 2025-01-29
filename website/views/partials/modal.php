<!-- MODAL MARKUP -->
<link rel="stylesheet" href="stylesheets/partials/modal.css">

<aside id="modal-window" style="display:none;">
    <div id="modal-wrapper">
        <div id="modal-menu" class="shadow">
            <span class="modal indigoTheme" style="border: none; padding-bottom: 10px;">
                <?php echo ($page == 'authenticationPage') ? 'Select a subject:' : 'Members:' ?>
            </span>
            <?php
            if ($page == 'authenticationPage') {
                foreach ($subjects as $subject)
                    echo '<td>' .
                        '<button class="indigoTheme modal" value="' .
                        $subject['SubjectCode'] . '">' .
                        $subject['SubjectName'] .
                        '</button>' .
                        '</td>';
            }
            ?>
        </div>
    </div>
</aside>

<script>
    const modal = document.getElementById('modal-window');
    const modalMenu = document.getElementById('modal-menu');

    function showModal() {
        modal.style.display = "";
        modal.animate([{
            opacity: "0"
        }, {
            opacity: "100"
        }], {
            duration: 200,
            easing: "ease-in-out"
        });
    }

    function hideModal() {
        modal.animate([{
            opacity: "100"
        }, {
            opacity: "0"
        }], {
            duration: 200,
            easing: "ease-in-out"
        });
        setTimeout(() => {
            modal.style.display = "none";
        }, 180);

    }

    sharedState.onSubjectDeselect = function showModalEntry(entryValue) {
        for (let entry of modalMenu.children) {
            if (entry.value == entryValue)
                entry.style.display = "";
        }
    }

    function hideModalEntry(entry) {
        entry.style.display = "none";
    }

    function addModalEntry(modalMenu, entry) {
        modalMenu.appendChild(entry);
    }

    function deleteModalEntry(entry) {
        entry.remove();
    }

    function selectSubject(entry) {
        // Get the selected subjects
        let selectedSubjectsInput = document.getElementById('selected-subjects');
        let selectedSubjectsArr = JSON.parse(selectedSubjectsInput.value);

        // Append new subject code to selected subjects array
        selectedSubjectsArr.push(entry.value);
        selectedSubjectsInput.value = JSON.stringify(selectedSubjectsArr);

        // Hide the selected entry from the modal menu
        this.hideModalEntry(entry);

        // Call subjectList to add the selected subject code to it
        sharedState.onSubjectSelect(entry.value);
    }


    document.addEventListener("DOMContentLoaded", () => {
        let selectModalEntry = (entry) => {};

        /* ---------------------- Authentication Page Specific Settings ------------------------- */
        <?php
        if ($page == 'authenticationPage') {
            // Set up the authenticationPage specific variables
            echo 'addSubjectButton = document.getElementById("addSubject-button");';
            echo 'selectModalEntry = selectSubject;';

            // Open the modal when user clicks to add subject & when the subjects selected is less than 5
            echo 'addSubjectButton.addEventListener("mousedown", () => {
                    if(sharedState.selectedSubjects < 5)
                        showModal();
                  })';
        }
        /* -------------------------------------------------------------------------------------- */
        ?>

        // Close the modal when user clicks anywhere
        modal.addEventListener("mousedown", () => {
            hideModal();
        });

        // Execute selectModalEntry function when user clicks on an entry
        modal.addEventListener("mousedown", event => {
            modalMenu.childNodes.forEach(entry => {
                if (event.target == entry && entry.nodeName != "SPAN")
                    selectModalEntry(entry);
            });
        });
    });
</script>