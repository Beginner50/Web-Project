document.addEventListener('DOMContentLoaded', () => {
    const chatManager = new ChatManager();
});

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