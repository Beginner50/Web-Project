
document.addEventListener("DOMContentLoaded", () => {
  const subjectManager = new SubjectManager();
  const moveableWrapperManager = new MoveableWrapperManager();
  const userTabManager = new UserTabManager();

  // Add event listener to registration form
  document.getElementById("registration-form").addEventListener("submit", () => {
    document.getElementById("selected-subjects").value = subjectManager.subjectsChosen.toString();
  });
});

/* ---------------------------------------------------------------------- */
class MoveableWrapperManager {
  constructor() {
    // Initialise values
    this.formStatus = "login";
    this.inTransit = false;

    this.loginElems = document.querySelectorAll(".login");
    this.registrationElems = document.querySelectorAll(".registration");

    this.CTAWrapper = document.querySelector("#callToAction-wrapper");
    this.formWrapper = document.querySelector("#form-wrapper");

    this.CTAH1 = this.CTAWrapper.querySelector("h1");
    this.CTAButton = document.querySelector("#callToAction-wrapper>button");
    this.CTAButtonLink = this.CTAButton.querySelector("a");
    this.formWrapperH1 = this.formWrapper.querySelector("h1");

    this.CTAkeyframesLeft = [
      { transform: "translateX(0%)" },
      { transform: "translateX(-150%)" }
    ];
    this.CTAkeyframesRight = [
      { transform: "translateX(-150%)" },
      { transform: "translateX(0%)" }
    ];

    this.formkeyframesRight = [
      { transform: "translate(0%)", opacity: "100" },
      { transform: "translate(5%)", opacity: "0" },
      { transform: "translate(55%)", opacity: "0" },
      { transform: "translate(65%)" }
    ];

    this.formkeyframesLeft = [
      { transform: "translate(65%)", opacity: "100" },
      { transform: "translate(65%)", opacity: "0" },
      { transform: "translate(10%)", opacity: "0" },
      { transform: "translate(-5%)" }
    ];

    this.options = {
      duration: 600,
      easing: "ease-in-out",
      fill: "forwards"
    };

    // Adds changeForm method to CTA button
    this.CTAButton.addEventListener("mousedown", () => {
      this.changeForm();
    });
  }

  // Change form from registration to login and vice-versa
  changeForm() {
    if (this.inTransit)
      return;

    this.inTransit = true;
    this.CTAButtonLink.href = "#main-wrapper";

    if (this.formStatus === "login")
      this.showRegistrationForm();
    else if (this.formStatus === "registration")
      this.showLoginForm();

    this.inTransit = false;
  }

  showLoginForm() {
    setTimeout(() => {
      this.loginElems.forEach(elem => { elem.style.display = "flex" });
      this.registrationElems.forEach(elem => { elem.style.display = "none" });
    }, 250);
    this.formWrapper.animate(this.formkeyframesLeft, this.options);
    this.CTAWrapper.animate(this.CTAkeyframesRight, this.options);

    this.CTAH1.textContent = "New to Education Portal?";
    this.CTAButtonLink.textContent = "Sign Up";
    this.formWrapperH1.textContent = "Sign In";

    this.formStatus = "login";
  }

  showRegistrationForm() {
    setTimeout(() => {
      this.loginElems.forEach(elem => { elem.style.display = "none" });
      this.registrationElems.forEach(elem => { elem.style.display = "block" });
    }, 250);
    this.formWrapper.animate(this.formkeyframesRight, this.options);
    this.CTAWrapper.animate(this.CTAkeyframesLeft, this.options);

    this.CTAH1.textContent = "Already Registered?";
    this.CTAButtonLink.textContent = "Sign In";
    this.formWrapperH1.textContent = "Sign Up";

    this.formStatus = "registration";
  }
}

/* ------------------------------------------------------------------------------------------- */

class SubjectManager {
  constructor() {
    this.numSubjectsChosen = 0;
    this.subjectsChosen = [];

    this.popUp = document.querySelector("div.popUp.window");
    this.popUpList = this.popUp.querySelector("div");
    this.formSubjectList = document.getElementById("subjectList");
    this.addSubjectButton = document.getElementById("addSubject-button");

    this.fetchSubjectList()
      .then(subjects => {
        subjects.forEach(subject => {
          const button = document.createElement("button");
          button.className = "indigoTheme popUp";
          button.value = subject;
          button.innerHTML = subject;
          this.popUpList.appendChild(button);
        })
      });

    // Assign event listener to the addSubject-button
    // When user clicks on add subject button, show the popup.
    this.addSubjectButton.addEventListener("mousedown", () => {
      if (this.numSubjectsChosen < 6) {
        this.popUp.style.display = "flex";
        this.popUp.animate([{ opacity: "0" }, { opacity: "100" }], { duration: 200, easing: "ease-in-out" });
      }
    });

    // Assign event listeners to popUp
    // When user clicks anywhere on the popup, close the popup
    this.popUp.addEventListener("mousedown", () => {
      this.popUp.animate([{ opacity: "100" }, { opacity: "0" }], { duration: 200, easing: "ease-in-out" });
      setTimeout(() => {
        this.popUp.style.display = "none";
      }, 180);
    });

    // When user clicks on a popup button, select the subject
    this.popUp.addEventListener("mousedown", event => {
      let subjectButtons = this.popUp.querySelectorAll("div>button");
      subjectButtons.forEach(subjectButton => {
        if (event.target == subjectButton)
          this.selectSubject(subjectButton);
      });
    });
  }

  // Fetch list of subjects from database asynchronously
  fetchSubjectList() {
    return fetch("php/getSubjects.php").then(response => response.json());
  }

  // User selects a subject from popUp menu
  selectSubject(subjectButton) {
    const subjectListEntry = document.createElement("div");
    subjectListEntry.className = "subject";
    const subjectEntryIcon = document.createElement("img");
    subjectEntryIcon.src = "icons/backspace.svg"
    subjectListEntry.innerHTML = subjectButton.value;
    subjectListEntry.appendChild(subjectEntryIcon);

    // Event delegation to handle click and hover events on subject close icon
    subjectEntryIcon.addEventListener("mouseenter", event => {
      event.target.src = "icons/backspaceRed.svg";
      event.target.parentElement.style.color = "red";
      event.target.parentElement.style.borderColor = "red";
    });
    subjectEntryIcon.addEventListener("mouseleave", event => {
      event.target.src = "icons/backspace.svg";
      event.target.parentElement.style.color = "var(--purpleVortex)";
      event.target.parentElement.style.borderColor = "var(--purpleVortex)";
    });
    subjectEntryIcon.addEventListener("mousedown", event => {
      this.unselectSubject(subjectEntryIcon.parentElement.textContent);
      subjectEntryIcon.parentElement.remove();
    });

    subjectButton.style.display = "none";

    this.addSubjectButton = this.formSubjectList.removeChild(this.formSubjectList.querySelector("button"));
    this.formSubjectList.appendChild(subjectListEntry);
    this.formSubjectList.appendChild(this.addSubjectButton);

    this.subjectsChosen.push(subjectButton.textContent);
    this.numSubjectsChosen++;
  }

  // User deselects a subject from the subject list in the form
  unselectSubject(subject) {
    let popUpButton = this.popUpList.querySelector(`button[value=${subject}]`);
    popUpButton.style.display = "inline";

    this.subjectsChosen = this.subjectsChosen.filter(elem => { return (elem != subject); });
    this.numSubjectsChosen--;
  }
}

/* ---------------------------------------------------------------------------- */
class UserTabManager {
  constructor() {
    this.userButtons = [
      document.getElementById("student-button"),
      document.getElementById("teacher-button"),
      document.getElementById("admin-button")
    ];
    this.userElems = [
      document.querySelectorAll(".student"),
      document.querySelectorAll(".teacher"),
      document.querySelectorAll(".admin")
    ];
    this.userTypes = ["Student", "Teacher", "Admin"];

    this.userTypeInput = document.getElementById("user-type");
    this.inTransit = false;

    this.userButtons.forEach(button => {
      button.addEventListener("mousedown", () => {
        if (this.inTransit)
          return;
        this.inTransit = true;

        let i = this.userButtons.findIndex(cmp => { return cmp === button; });
        this.showTab(i);

        this.inTransit = false;
      });
    })
  }

  showTab(i) {
    this.userButtons[i].classList.add("active");
    this.userButtons[(i + 1) % 3].classList.remove("active");
    this.userButtons[(i + 2) % 3].classList.remove("active");

    setTimeout(() => {
      this.userElems[i].forEach(elem => { elem.style.display = "flex"; });
    }, 200)
    this.userElems[(i + 1) % 3].forEach(elem => { elem.style.display = "none"; });
    this.userElems[(i + 2) % 3].forEach(elem => { elem.style.display = "none" });

    this.userTypeInput.value = this.userTypes[i];
  }
}