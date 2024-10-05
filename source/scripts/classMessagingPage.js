import PopUpManager from '../scripts/popUp.js';

document.addEventListener('DOMContentLoaded', () => {
    const classMessagingController = new ClassMessagingController();
});
/*
    MVC - Controller is the intermediary between the view and the model
          It also handles user requests and calls the view and model accordingly
*/
class ClassMessagingController {
    classMessagingView;
    classMessagingModel;
    popUpManager;

    classListMenu;
    classMessageInput;
    sendMessageButton;
    viewMembersButton;

    constructor() {
        this.classMessagingView = new ClassMessagingView(new PopUpManager());
        this.classMessagingModel = new ClassMessagingModel();
        this.popUpManager = new PopUpManager();

        this.addClassMenuFunctionality();
        this.addViewMembersFunctionality();
        this.addSendMessageFunctionality();
    }


    addClassMenuFunctionality() {
        this.classListMenu = document.getElementById("class-menu");

        // Updates the class view
        let updateClassView = child => {
            this.classMessagingView.hideClassChatCover();
            this.classMessagingView.updateActiveMenuElement(child);
            this.classMessagingView.updateClassDescription(child.textContent);

            this.classMessagingModel.getClassMessages()
                .then(messages => this.classMessagingView.updateClassMessages(messages));

            this.classMessagingModel.getClassMembers()
                .then(members => this.classMessagingView.updateClassMembers(members));
        }

        // Finds out which menu entry is the target of the click event
        this.classListMenu.addEventListener("mousedown", event => {
            this.classListMenu.childNodes.forEach(child => {
                if (event.target == child) {
                    this.classMessagingModel.setClassID(child.getAttribute("data-classID"));
                    updateClassView(child);
                }
            })
        });
    }

    addSendMessageFunctionality() {
        this.classMessageInput = document.getElementById("message-input");
        this.sendMessageButton = document.getElementById("send-button");

        this.sendMessageButton.addEventListener('mousedown', () => {
            if (this.classMessageInput.value === "")
                return;
            this.classMessagingModel.sendClassMessage(this.classMessageInput.value)
                .then(() => this.classMessagingModel.getClassMessages())
                .then(messages => this.classMessagingView.updateClassMessages(messages));
            this.classMessagingView.updateClassMessageInput();
        });
    }

    addViewMembersFunctionality() {
        this.viewMembersButton = document.getElementById("viewMembers-button");

        this.viewMembersButton.addEventListener("mousedown", () => {
            this.popUpManager.showPopUp();
        });
    }
}

/*
    MVC - Model handles only logic related to backend data
*/
class ClassMessagingModel {
    currentClassID;
    constructor() { }

    setClassID(currentClassID) {
        this.currentClassID = Number(currentClassID);
    }

    getClassID() {
        return this.currentClassID;
    }

    getClassMessages() {
        const data = `ClassID=${this.currentClassID}`;
        return fetch("ClassMessaging/getClassMessages.php", {
            method: "POST",
            headers: {
                "Content-Type": "application/x-www-form-urlencoded",
                "Cache-control": "no-cache"
            },
            body: new URLSearchParams(data)
        }).then(response => response.json());
    }

    getClassMembers() {
        const data = `ClassID=${this.currentClassID}`;
        return fetch("ClassMessaging/getClassMembers.php", {
            method: "POST",
            headers: {
                "Content-Type": "application/x-www-form-urlencoded",
            },
            body: new URLSearchParams(data)
        }).then(response => response.json());
    }

    sendClassMessage(Message) {
        const data = `ClassID=${this.currentClassID}&message-input=${Message}`;
        return fetch("ClassMessaging/sendMessage.php", {
            method: "POST",
            header: {
                "Content-Type": "application/x-www-form-urlencoded",
            },
            body: new URLSearchParams(data)
        });
    }
}

/*
    MVC - View handles only UI logic
*/
class ClassMessagingView {
    popUpManager;

    classListMenu;
    classChatCover;

    classChatHeader;
    classChatDescription;

    classChatBody;

    classChatFooter;
    classMessageInput;

    classChatAll;

    constructor() {
        this.popUpManager = new PopUpManager();

        this.classListMenu = document.getElementById("class-menu");
        this.classChatCover = document.getElementById("classChat-cover");

        this.classChatHeader = document.getElementById("classChat-header");
        this.classChatDescription = this.classChatHeader.querySelector("#classChat-description");

        this.classChatBody = document.getElementById("classChat-body");

        this.classChatFooter = document.getElementById("classChat-footer");
        this.classMessageInput = document.getElementById("message-input");

        this.classChatAll = document.querySelectorAll(".classChat");

        this.classChatCoverVisible = true;
    }

    showClassChatCover() {
        if (this.classChatCoverVisible)
            return;
        this.classChatCoverVisible = true;

        this.classChatCover.style.display = "";
        this.classChatAll.forEach(elem => { elem.style.display = "none"; });
    }

    hideClassChatCover() {
        if (!this.classChatCoverVisible)
            return;
        this.classChatCoverVisible = false;

        let keyframes = [
            { opacity: "100" },
            { opacity: "0" }
        ];
        let options = {
            duration: 300,
            easing: "ease-in-out",
            fill: "forwards"
        };

        this.classChatCover.animate(keyframes, options);
        setTimeout(() => {
            this.classChatCover.style.display = "none";
            this.classChatAll.forEach(elem => { elem.style.display = ""; });
        }, 300);
    }

    updateActiveMenuElement(element) {
        // Deactivates any previously active list elements
        let activeButton = this.classListMenu.querySelector('.active');
        if (activeButton != null)
            activeButton.classList.remove('active');

        // Activate current list element
        element.classList.add('active');
    }

    updateClassMembers(classMembers) {
        // Delete previous member entries
        document.querySelectorAll('section.popUp').forEach(member => this.popUpManager.deleteMenuItem(member));

        let createMember = member => {
            const section = document.createElement('section');
            section.classList.add('popUp');

            const name = document.createElement('span');
            name.className = "popUp";
            const userType = document.createElement('span');
            userType.className = "popUp";

            section.appendChild(name);
            section.appendChild(userType);

            name.textContent = member['FirstName'] + ' ' + member['LastName'];
            userType.textContent = member["UserType"];

            this.popUpManager.addMenuItem(section);
        }

        // Add new class member entry
        try {
            classMembers.forEach(member => {
                createMember(member);
            });
        } catch (e) {
            if (e.name == TypeError)
                createMember(classMembers);
        }
    }

    updateClassMessages(classMessages) {
        // Delete previous message entries
        document.querySelectorAll('.message').forEach(message => message.remove());

        let keyframes = [
            { transform: "translate(-100px)", opacity: "0" },
            { transform: "translate(0px)", opacity: "100" }
        ];

        let options = {
            duration: 300,
            easing: "ease-in-out",
            fill: "forwards"
        };

        let createMessage = message => {
            const span = document.createElement('span');
            span.className = 'message';
            span.textContent = message['Message'];
            span.animate(keyframes, options);
            this.classChatBody.appendChild(span);
        }


        try {
            classMessages.forEach(message => {
                createMessage(message)
            })
        }
        catch (e) {
            if (e.name == TypeError)
                createMessage(classMessages);
        }
    }

    updateClassDescription(classDescription) {
        this.classChatDescription.textContent = classDescription;
    }

    updateClassMessageInput() {
        this.classMessageInput.value = "";
    }
}

