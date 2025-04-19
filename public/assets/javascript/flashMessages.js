let flashMessages = document.getElementsByClassName("flash-message");
console.log(flashMessages);
for (let flashMessage of flashMessages) {
    setTimeout(() => {
        flashMessage.style.transition = "opacity 0.5s";
        flashMessage.style.opacity = "0";

        setTimeout(() => flashMessage.remove(), 500);
    }, 5000);
}