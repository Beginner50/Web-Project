import PopUpManager from './popUp.js';

document.addEventListener("DOMContentLoaded", () => {
  const authenticationController = new AuthenticationController();
});

/*
  MVC - Controller acts as the intermediary between model and view components
        More importantly, it receives the user's requests and updates the view
        and model respectively
*/
class AuthenticationController {
  authenticationModel
  authenticationView;
  popUpManager;

  CTAButton;
  subjectButton;
  userTypeButtons;

  constructor() {
    this.popUpManager = new PopUpManager();
    this.authenticationModel = new AuthenticationModel();
    this.authenticationView = new AuthenticationView(this.popUpManager);

    this.CTAButton = document.querySelector("#callToAction-wrapper>button");
    this.subjectButton = document.querySelector("#addSubject-button");
    this.userTypeButtons = [
      document.getElementById("student-button"),
      document.getElementById("teacher-button"),
      document.getElementById("admin-button")
    ];

    this.addCTAButtonFunctionality();
    this.addPopUpMenuFunctionality();
    this.addUserTypeButtonFunctionality();
    this.addSubjectButtonFunctionality();
  }

  addCTAButtonFunctionality() {
    /*
      Upon clicking Sign In/Up button, changes and gets new form status from model
      and updates the state of the form in the view
    */
    this.CTAButton.addEventListener("mousedown", () => {
      let newformStatus = this.authenticationModel.newFormStatus();
      this.authenticationView.updateFormView(newformStatus);
    });
  }

  addUserTypeButtonFunctionality() {
    /*
      When user clicks on a userType button, get the index of the button clicked and update
      the user specific fieldset of the registration form accordingly.
    */
    this.userTypeButtons.forEach(button => {
      button.addEventListener("mousedown", () => {
        let currentTab = this.userTypeButtons.findIndex(cmp => { return cmp === button; });
        this.authenticationView.updateUserTab(currentTab);
        this.authenticationView.updateUserTypeInput(button.value);
      });
    })
  }

  addSubjectButtonFunctionality() {
    this.subjectButton.addEventListener("mousedown", () => {
      if (this.authenticationModel.numSujectsChosen < 5) {
        this.popUpManager.showPopUp();
      }
    });
  }

  addSubjectEntryFunctionality(subjectListEntry) {
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
    subjectListEntryIcon.addEventListener("mousedown", event => {
      /*
        If user clicks on icon, delete the corresponding subject entry
        from the subject list
      */
      let subjectCode = subjectListEntryIcon.parentElement.textContent;

      this.authenticationModel.removeSubject(subjectCode);
      this.authenticationView.updateSubjectList(subjectCode, "DELETE");
    });
  }

  addPopUpMenuFunctionality() {
    // Dependency injection of an event into popUp since popUp does not need to be aware of the caller
    this.popUpManager.buttonEvent((button) => {
      let subjectCode = button.value;
      this.authenticationModel.addSubject(subjectCode);

      let subjectListEntry = this.authenticationView.updateSubjectList(subjectCode, "CREATE");
      this.authenticationView.updateSubjectsChosenInput(this.authenticationModel.subjectsChosen);

      this.addSubjectEntryFunctionality(subjectListEntry);
    });
  }
}

/*
  MVC - View handles only the UI logic
*/
class AuthenticationView {
  popUpManager;

  CTAWrapper;
  formWrapper;

  inTransit = false;
  CTAkeyframesLeft = [
    { transform: "translateX(0%)" },
    { transform: "translateX(-150%)" }
  ];
  CTAkeyframesRight = [
    { transform: "translateX(-150%)" },
    { transform: "translateX(0%)" }
  ];
  formkeyframesRight = [
    { transform: "translate(0%)", opacity: "100" },
    { transform: "translate(5%)", opacity: "0" },
    { transform: "translate(55%)", opacity: "0" },
    { transform: "translate(65%)" }
  ];
  formkeyframesLeft = [
    { transform: "translate(65%)", opacity: "100" },
    { transform: "translate(65%)", opacity: "0" },
    { transform: "translate(10%)", opacity: "0" },
    { transform: "translate(0%)" }
  ];
  options = {
    duration: 600,
    easing: "ease-in-out",
    fill: "forwards"
  };

  constructor(popUpManager) {
    this.popUpManager = popUpManager;

    this.CTAWrapper = document.querySelector("#callToAction-wrapper");
    this.CTAH1 = this.CTAWrapper.querySelector("h1");
    this.CTAButton = document.querySelector("#callToAction-wrapper>button");
    this.CTAButtonLink = this.CTAButton.querySelector("a");

    this.formWrapper = document.querySelector("#form-wrapper");
    this.formWrapperH1 = this.formWrapper.querySelector("h1");

    // Login form
    this.loginForm = document.querySelector("#login-form");

    // Registration Form
    this.registrationForm = document.querySelector("#registration-form");
    this.formSubjectList = document.getElementById("subjectList");
    this.addSubjectButton = document.getElementById("addSubject-button");
    this.subjectsChosenInput = document.getElementById("selected-subjects");

    this.userButtons = [
      document.getElementById("student-button"),
      document.getElementById("teacher-button"),
      document.getElementById("admin-button")
    ];
    this.userFieldsets = [
      document.querySelector("#specificAttr-fieldset-student"),
      document.querySelector("#specificAttr-fieldset-teacher"),
      document.querySelector("#specificAttr-fieldset-admin")
    ];
  }

  updateFormView(newFormStatus) {
    if (this.inTransit)
      return;

    this.inTransit = true;
    if (newFormStatus === "login")
      this.#showLoginForm();
    else if (newFormStatus === "registration")
      this.#showRegistrationForm();
  }

  updateSubjectList(subjectCode, action) {
    if (action === "DELETE") {
      // Deletes subjectEntry from subjectList with given subjectCode
      this.formSubjectList.childNodes.forEach(child => {
        if (child.textContent == subjectCode)
          this.#deleteSubjectListEntry(child);
      });

      // Shows corresponding popUp button
      let popUpButton = this.popUpManager.getMenuItem(subjectCode);
      this.popUpManager.showButton(popUpButton);
    }
    else if (action === "CREATE") {
      // Creates subject entry
      let subjectListEntry = this.#createSubjectListEntry(subjectCode);

      // Re-arranges order of the add button (Always place it ahead of subject entries)
      this.addSubjectButton = this.formSubjectList.removeChild(this.formSubjectList.querySelector("button"));
      this.formSubjectList.appendChild(subjectListEntry);
      this.formSubjectList.appendChild(this.addSubjectButton);

      return subjectListEntry;
    }
    return null;
  }

  updateSubjectsChosenInput(subjectsChosen) {
    this.subjectsChosenInput.value = "";
    this.subjectsChosenInput.value = subjectsChosen;
  }

  updateUserTypeInput(userType) {
    document.getElementById("user-type").value = userType;
  }

  updateUserTab(currentTab) {
    if (this.inTransit)
      return;
    this.inTransit = true;

    this.userButtons[currentTab].classList.add("active");
    this.userButtons[(currentTab + 1) % 3].classList.remove("active");
    this.userButtons[(currentTab + 2) % 3].classList.remove("active");

    setTimeout(() => {
      this.userFieldsets[currentTab].style.display = ""; this.userFieldsets[currentTab].disabled = false;
    }, 200)
    this.userFieldsets[(currentTab + 1) % 3].style.display = "none"; this.userFieldsets[(currentTab + 1) % 3].disabled = true;
    this.userFieldsets[(currentTab + 2) % 3].style.display = "none"; this.userFieldsets[(currentTab + 2) % 3].disabled = true;

    this.inTransit = false;
  }

  #showLoginForm() {
    setTimeout(() => {
      this.loginForm.style.display = "";
      this.registrationForm.style.display = "none";
      this.inTransit = false;
    }, 250);
    this.formWrapper.animate(this.formkeyframesLeft, this.options);
    this.CTAWrapper.animate(this.CTAkeyframesRight, this.options);

    this.CTAH1.textContent = "New to Education Portal?";
    this.CTAButtonLink.textContent = "Sign Up";
    this.formWrapperH1.textContent = "Sign In";
  }

  #showRegistrationForm() {
    setTimeout(() => {
      this.loginForm.style.display = "none";
      this.registrationForm.style.display = "";
      this.inTransit = false;
    }, 250);
    this.formWrapper.animate(this.formkeyframesRight, this.options);
    this.CTAWrapper.animate(this.CTAkeyframesLeft, this.options);

    this.CTAH1.textContent = "Already Registered?";
    this.CTAButtonLink.textContent = "Sign In";
    this.formWrapperH1.textContent = "Sign Up";
  }

  #createSubjectListEntry(subjectCode) {
    const subjectListEntry = document.createElement("div");
    subjectListEntry.className = "subject";
    const subjectListEntryIcon = document.createElement("img");
    subjectListEntryIcon.src = "assets/backspace.svg"
    subjectListEntry.textContent = subjectCode;
    subjectListEntry.appendChild(subjectListEntryIcon);

    return subjectListEntry;
  }

  #deleteSubjectListEntry(subjectListEntry) {
    subjectListEntry.remove();
  }
}

/*
  MVC - Model hanldes only the state logic of the page
*/
class AuthenticationModel {
  formStatus = "login";
  subjectsChosen = [];
  numSujectsChosen = 0;

  newFormStatus() {
    if (this.formStatus == "login")
      this.formStatus = "registration";
    else if (this.formStatus == "registration")
      this.formStatus = "login"

    return this.formStatus;
  }

  addSubject(subject) {
    this.subjectsChosen.push(subject);
    this.numSujectsChosen++;
  }

  removeSubject(subject) {
    this.subjectsChosen = this.subjectsChosen.filter(elem => {
      return elem != subject
    });
    this.numSujectsChosen--;
  }

}
