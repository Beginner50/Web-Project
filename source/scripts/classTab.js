document.addEventListener('DOMContentLoaded', () => {
    const chatManager = new ChatManager();
    const classListManager = new ClassListManager();

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

// Incomplete
class ClassListManager {
    constructor() {
        this.menu = document.getElementById('class-menu');
        this.classChat = document.getElementById('classChat-body');

        fetch("Classes/getClasses.php")
            .then(response => response.json())
            .then(classMap => this.addClassesToMenu(classMap));

        // Optimise this into functions to getMessage and addToChat
        this.menu.addEventListener("mousedown", event => {
            this.menu.childNodes.forEach(child => {
                if (event.target == child) {
                    const parts = child.innerHTML.split(',');
                    const subjectCode = parts[0].trim();
                    const level = parts[1][parts[1].length - 1];
                    const classGroup = parts[2].trim();

                    // Get class messages
                    const xhr = new XMLHttpRequest();
                    xhr.open("POST", "Classes/getClassMessages.php", true);
                    xhr.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');

                    const data = `SubjectCode=${subjectCode}&Level=${level}&ClassGroup=${classGroup}`;
                    xhr.send(data);

                    xhr.onreadystatechange = () => {
                        if (xhr.readyState === 4 && xhr.status === 200) {
                            document.querySelectorAll('.message').forEach(message => message.remove());
                            const messages = JSON.parse(xhr.response);
                            messages.forEach(message => {
                                const span = document.createElement('span');
                                span.className = 'message';
                                span.innerHTML = message['Message'];
                                this.classChat.appendChild(span);
                            })
                        }
                    };
                    return;
                }
            });
        });
    }

    addClassesToMenu(classMap) {
        classMap.forEach(classMap => {
            const ul = document.createElement('ul');
            ul.innerHTML = classMap.SubjectCode + ", Level " + classMap.Level + ", " + classMap.ClassGroup;
            this.menu.appendChild(ul);
        });
    }
}

class ChatManager {
    constructor() {
        this.classMessageElems = document.querySelectorAll(".classChat");
    }

    showClassMessageTab() {
        this.classMessageElems.forEach(elem => { elem.style.display = ""; });
    }

    hideClassMessageTab() {
        this.classMessageElems.forEach(elem => { elem.style.display = "none"; });
    }
}