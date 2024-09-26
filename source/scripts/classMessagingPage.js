import PopUpManager from '../scripts/popUp.js';

document.addEventListener('DOMContentLoaded', () => {
    const chatManager = new ChatManager(new PopUpManager());
    const classListManager = new ClassListManager(chatManager);

    const sendButton = document.getElementById('send-button');
    sendButton.addEventListener('mouseenter', () => {
        sendButton.style.background = 'rgba(205, 25, 100, 0.6)';
    });
    sendButton.addEventListener('mouseleave', () => {
        sendButton.style.background = 'rgba(255,255,255,0.6)';
    });
    sendButton.addEventListener('mousedown', () => {
        sendButton.style.background = 'rgba(205, 25, 100, 0.8)';
    });
    sendButton.addEventListener('mouseup', () => {
        sendButton.style.background = 'rgba(205, 25, 100, 0.6)';
    });

});

class ClassListManager {
    constructor(classChatManager) {
        this.menu = document.getElementById('class-menu');
        this.classChatManager = classChatManager;

        // Get classes
        fetch("ClassMessaging/getClasses.php")
            .then(response => response.json())
            .then(classAttributes => {
                if (classAttributes != false) {
                    this.addClassesToMenu(classAttributes)
                }
                else {
                    console.log("You don't have any classes yet!");
                }
            });

        // Only send classID
        this.menu.addEventListener("mousedown", event => {
            this.menu.childNodes.forEach(child => {
                if (event.target == child) {
                    this.classChatManager.setClassID(child.value);

                    this.classChatManager.refreshClassMessages();
                    this.classChatManager.refreshClassMembers();
                }
            })
        });
        return;
    }

    addClassesToMenu(classMap) {
        try {
            classMap.forEach(classMap => {
                const ul = document.createElement('ul');
                ul.innerHTML = classMap.SubjectCode + ", Level " + classMap.Level + ", " + classMap.ClassGroup;
                ul.nodeValue = classMap.ClassID;
                this.menu.appendChild(ul);
            });
        }
        catch (err) {
            if (err.name == "TypeError") {
                const ul = document.createElement('ul');
                ul.innerHTML = classMap.SubjectCode + ", Level " + classMap.Level + ", " + classMap.ClassGroup;
                ul.value = classMap.ClassID;
                this.menu.appendChild(ul);
            }
        }
    }
}

class ChatManager {
    constructor(popUpManager) {
        this.classID = 0;

        this.classChat = document.getElementById('classChat-body');
        this.classMessageElems = document.querySelectorAll(".classChat");
        this.popUpManager = popUpManager;
        this.viewMembersButton = document.getElementById('viewMembers-button');
        this.sendButton = document.getElementById('send-button');

        // Assign event listener to view members button
        this.viewMembersButton.addEventListener("mousedown", () => {
            this.popUpManager.showPopUp();
        });

        // Assign event listener to send button
        this.sendButton.addEventListener('mousedown', () => {
            const xhr = new XMLHttpRequest();

            xhr.open("POST", "ClassMessaging/sendMessage.php", true);
            xhr.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');

            let messageInput = document.getElementById('message-input');
            let Message = messageInput.value;
            const data = `ClassID=${this.classID}&message-input=${Message}`;
            xhr.send(data);
            messageInput.value = "";

            this.refreshClassMessages();
        });
    }

    setClassID(classID) {
        this.classID = classID;
    }

    getClassID() {
        return this.classID;
    }

    refreshClassMembers() {
        // Get class members
        let xhr = new XMLHttpRequest();
        xhr.open("POST", "ClassMessaging/getClassMembers.php", true);
        xhr.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');

        const data = `ClassID=${this.classID}`;
        xhr.send(data);

        xhr.onreadystatechange = () => {
            if (xhr.readyState === 4 && xhr.status === 200) {
                // Remove previous class members in the list
                document.querySelectorAll('section.popUp').forEach(member => this.deleteMemberEntry(member));

                // Get and create new members
                try {
                    const members = JSON.parse(xhr.response);
                    members.forEach(member => {
                        this.createMemberEntry(member);
                    })
                }
                catch (e) {

                }
            }
        };
    }

    refreshClassMessages() {
        // Get class messages
        const xhr = new XMLHttpRequest();
        xhr.open("POST", "ClassMessaging/getClassMessages.php", true);
        xhr.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
        xhr.setRequestHeader('Cache-Control', 'no-cache');

        const data = `ClassID=${this.classID}`;
        xhr.send(data);

        xhr.onreadystatechange = () => {
            if (xhr.readyState === 4 && xhr.status === 200) {
                // Remove all previous messages
                document.querySelectorAll('.message').forEach(message => message.remove());

                const messages = JSON.parse(xhr.response);
                messages.forEach(message => {
                    const span = document.createElement('span');
                    span.className = 'message';
                    span.innerHTML = message['Message'];
                    this.classChat.appendChild(span);
                })
            }
        }
    }

    createMemberEntry(member) {
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

    deleteMemberEntry(member) {
        member.remove();
    }

    showClassMessageTab() {
        this.classMessageElems.forEach(elem => { elem.style.display = ""; });
    }

    hideClassMessageTab() {
        this.classMessageElems.forEach(elem => { elem.style.display = "none"; });
    }
}