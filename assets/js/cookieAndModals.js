/** FUNCTION THAT READS A COOKIE */
function readCookie(name) {
    var cookiename = name + "=";
    var ca = document.cookie.split(';');
    for (var i = 0; i < ca.length; i++) {
        var c = ca[i];
        while (c.charAt(0) == ' ') c = c.substring(1, c.length);
        if (c.indexOf(cookiename) == 0) return c.substring(cookiename.length, c.length);
    }
    return null;
}

/** FUNCTION THAT CHANGES THE VALUE OF A COOKIE */
function setCookie(cname, cvalue, exdays, path) {
    var d = new Date();
    d.setTime(d.getTime() + (exdays * 24 * 60 * 60 * 1000));
    var expires = "expires=" + d.toUTCString();
    document.cookie = cname + "=" + cvalue + ";" + expires + ";path=/" + path;
}

/** FUNCTION THAT REMOVES A COOKIE */
function eraseCookie(name, path) {
    document.cookie = name+'=; Max-Age=-99999999;path=/' + path;
}

(function($) {
    if (readCookie("newAccount") === "true")
    {
        Swal.fire({
            icon: 'success',
            text: 'Compte crée avec succes',
        }).then(() => {
            eraseCookie("newAccount", ""); //if 3rd arg is 0, the cookie is removed
        });
    }
    if (readCookie("forgottenPasswordValidEmail") === "true")
    {
        Swal.fire({
            icon: 'success',
            title: 'Mot de passe reinitialisé',
            text: 'Si l\'adresse mail fournie est valide alors un lien de reinitialisation de mot de passe vous a été envoyé',
        }).then(() => {
            eraseCookie("forgottenPasswordValidEmail", "login"); //if 3rd arg is 0, the cookie is removed
        });
    }
    if (readCookie("loginFail") === "true")
    {
        Swal.fire({
            icon: 'error',
            text: 'Adresse email ou mot de passe inccorect',
        }).then(() => {
            eraseCookie("loginFail", "login");
        });
    }
    if (readCookie("failedCaptcha") === "true")
    {
        Swal.fire({
            icon: 'error',
            text: 'Erreur lors de la soumission du captcha',
        }).then(() => {
            eraseCookie("failedCaptcha", "login");
        });
    }
    if (readCookie("tempLoginEmail"))
    {
        $("#email").val(readCookie("tempLoginEmail"));
        eraseCookie("tempLoginEmail", "login");
    }
    if (readCookie("tempLoginPassword"))
    {
        $("#pwd").val(readCookie("tempLoginPassword"));
        eraseCookie("tempLoginPassword", "login");
    }
    if (readCookie("anotherLoginDetected"))
    {
        Swal.fire({
            icon: 'error',
            text: 'Une autre connexion avec vos identifiants a été detectée. Déconnexion en cours.',
        }).then(() => {
            eraseCookie("anotherLoginDetected", "");
        });
    }


})( window.jQuery );
