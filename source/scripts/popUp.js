export default class PopUpManager {
    // Adds the required event listeners
    constructor() {
        this.popUp = document.getElementById('popUp-window');
        this.popUpMenu = document.getElementById('popUp-menu');

        // Assign event listeners to popUp
        // When user clicks anywhere on the popup, close the popup
        this.popUp.addEventListener("mousedown", () => {
            this.hidePopUp();
        });

        // When user clicks on a popup button, select the subject (Called by the aggregate)
    }

    // Interface
    showPopUp() {
        this.popUp.style.display = "";
        this.popUp.animate([{ opacity: "0" }, { opacity: "100" }], { duration: 200, easing: "ease-in-out" });
    }

    hidePopUp() {
        this.popUp.animate([{ opacity: "100" }, { opacity: "0" }], { duration: 200, easing: "ease-in-out" });
        setTimeout(() => {
            this.popUp.style.display = "none";
        }, 180);

    }

    buttonEvent(eventFunction) {
        this.popUp.addEventListener("mousedown", event => {
            this.popUpMenu.childNodes.forEach(button => {
                if (event.target == button) {
                    eventFunction(button);
                    this.hideButton(button);
                }
            });
        });
    }

    showButton(button) {
        button.style.display = "";
    }

    hideButton(button) {
        button.style.display = "none";
    }

    addButton(button) {
        this.popUpMenu.appendChild(button);
    }

    getButton(value) {
        return this.popUpMenu.querySelector(`button[value=${value}]`);
    }

    setElementVisibility(elem, visibility) {
        elem.visibility = (visibility == "none" ? "none" : "");
    }

}
