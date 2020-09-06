function disableCheaperSubscriptions(activeSubscription) {
    let subscriptions = document.querySelectorAll('.card');
    let buyButtons = document.querySelectorAll('.btnAchat');

    for (let i = 0; i < activeSubscription; i++) {
            subscriptions[i].classList.add("disabledSubscription"); //gray out card
            buyButtons[i].classList.add("disabled"); //gray out card
            buyButtons[i].style.opacity = "0.3"; //gray out card
            buyButtons[i].removeAttribute('onclick');
    }
}