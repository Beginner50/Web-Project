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

        fetch("Classes/getClasses.php")
            .then(response => response.json())
            .then(classMap => this.addToMenu(classMap));
    }

    addToMenu(classMap) {
        classMap.forEach(classMap => {
            classEntry = document.createElement('ul');
            classEntry.innerHTML = classMap['SubjectCode'];
            console.log(classMap);
            this.menu.appendChild(classEntry);
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