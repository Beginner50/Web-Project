function redirectToresetPass() {

    window.location.href = "../AdminPage/resetPass.php";
}
function redirectToverifyAcc() {

    window.location.href = "../AdminPage/verifyAcc.php";
}

let userList = document.querySelector('.user-list');
userList.addEventListener('mousedown', event => {
    userList.childNodes.forEach(child => {
        if (child == event.target)
            window.location.href = "infoRetrieve.php";
        document.querySelector('.userinfo-container').style.display = "";
        userList.style.display = "none";
    });
});