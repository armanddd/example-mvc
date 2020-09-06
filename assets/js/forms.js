/** FUNCTION THAT CHECKS THE LOGIN FORM */
function submitLoginForm(captchaExists) {
    let email = $("#email").val();
    let pwd = $("#pwd").val();
    let mailRegEx = /^(([^<>()\[\]\.,;:\s@\"]+(\.[^<>()\[\]\.,;:\s@\"]+)*)|(\".+\"))@(([^<>()[\]\.,;:\s@\"]+\.)+[^<>()[\]\.,;:\s@\"]{2,})$/i;
    if (!mailRegEx.test(String(email).toLowerCase()) || !email) {
        Swal.fire({
            icon: 'error',
            text: 'L\'email est invalide',
        });
        return false;
    }
    if (!pwd) {
        Swal.fire({
            icon: 'error',
            text: 'Le mot de passe est invalide',
        });
        return false;
    }

    if (captchaExists)
        var data = {
            'email': email,
            'pwd': pwd,
            'action': 'login',
            'captcha' : grecaptcha.getResponse()
        };
    else {
        var data = {
            'email': email,
            'pwd': pwd,
            'action': 'login'
        };
    }

    /*requete ajax pour le formulaire*/
    $.ajax({
        url: '/forms-processing',
        type: 'POST',
        data: data,
        success: function (res) {
            res = $.parseJSON(res);

            switch(res){
                case "failedCaptcha":
                    //set cookie to show fail login modal and reload page to show captcha
                    setCookie("failedCaptcha", true, 1, "login");
                    setCookie("tempLoginEmail", $("#email").val(), 1, "login");
                    setCookie("tempLoginPassword", $("#pwd").val(), 1, "login");
                    window.location.reload();
                    break;
                case "loginSuccesful":
                    //redirect in case of success
                    window.location.replace('/');
                    break;
                case "loginFail":
                    //show fail login modal
                    setCookie("loginFail", true, 1, "login");
                    setCookie("tempLoginEmail", $("#email").val(), 1, "login");
                    window.location.reload();
                    break;
                case "loginFailWithCaptcha":
                    //show fail login modal and register email and pwd
                    setCookie("loginFail", true, 1, "login");
                    setCookie("tempLoginEmail", $("#email").val(), 1, "login");
                    setCookie("tempLoginPassword", $("#pwd").val(), 1, "login");
                    window.location.reload();
                    break;
            }
        },
        error: function (res) {
        }
    });
    return false;
}


/* FUNCTION THAT CHECKS THE REGISTRATION FORM */
function submitRegisterForm() {
    let name = $("#inputName").val();
    let surname = $("#inputSurname").val();
    let email = $("#inputEmail").val();
    let mailRegEx = /^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
    let phone = $("#inputTel").val();
    let pw = $("#inputPassword3").val();
    let pwVerify = $("#inputVerify3").val();

    if (!mailRegEx.test(String(email).toLowerCase()) || !email) {
        Swal.fire({
            icon: 'error',
            text: 'L\'email est invalide',
        });
        return false;
    }

    if (name.length < 2) {
        Swal.fire({
            icon: 'error',
            text: 'Le nom est invalide',
        });
        return false;
    }

    if (surname.length < 2) {
        Swal.fire({
            icon: 'error',
            text: 'Le prenom est invalide',
        });
        return false;
    }

    if (phone.length < 9 || phone.length > 13) {
        Swal.fire({
            icon: 'error',
            text: 'Le numero de telephone doit contenir entre 9 et 13 chiffres',
        });
        return false;
    }

    if (!(/^[0-9]*$/.test(phone))) {
        Swal.fire({
            icon: 'error',
            text: 'Le numero de telephone doit contenir que des chiffres',
        });
        return false;
    }

    if (pw.length < 7 || pw.length > 17) {
        Swal.fire({
            icon: 'error',
            text: 'Le mot de passe doit contenir entre 7 et 17 caractères',
        });
        return false;
    }

    if (pw !== pwVerify) {
        Swal.fire({
            icon: 'error',
            text: 'Les mots de passe ne correspondent pas',
        });
        return false;
    }

    /*requete ajax pour le formulaire*/
    $.ajax({
        url: '/forms-processing',
        type: 'POST',
        data: {
            'name': name,
            'surname': surname,
            'email': email,
            'phone': phone,
            'pw': pw,
            'pwVerify': pwVerify,
            'action': 'register'
        },
        success: function (res) {
            res = $.parseJSON(res);
            switch (res) {
                case "emailExist":
                    Swal.fire({
                        icon: 'error',
                        text: 'L\'adresse mail est déja utilisée',
                    });
                    break;
                case "phoneExists":
                    Swal.fire({
                        icon: 'error',
                        text: 'Le numéro de téléphone est déja utilisé',
                    });
                    break;
                case "genericFormError":
                    Swal.fire({
                        icon: 'error',
                        text: 'Une erreure est survenue. Veuillez réessayer plus tard.',
                    });
                    break;
                case "accountRegistered":
                    window.location.replace('/');
                    break;
            }
        },
        error: function (res) {

        }
    });
    return false;
}

/* FUNCTION THAT CHECKS THE PROFILE FORM */
function submitProfileForm(){
    let name = $("#nameInput").val();
    let surname = $("#surnameInput").val();
    let email = $("#emailInput").val();
    let mailRegEx = /^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
    let phone = $("#telephoneInput").val();
    let pw = $("#passwordInput").val();
    let newPw = $("#newPasswordInput").val();
    let newPwVerify = $("#newPasswordVerifyInput").val();

    if (!mailRegEx.test(String(email).toLowerCase()) || !email) {
        Swal.fire({
            icon: 'error',
            text: 'L\'email est invalide',
        });
        return false;
    }

    if (name.length < 2) {
        Swal.fire({
            icon: 'error',
            text: 'Le nom est invalide',
        });
        return false;
    }

    if (surname.length < 2) {
        Swal.fire({
            icon: 'error',
            text: 'Le prenom est invalide',
        });
        return false;
    }

    if (phone.length < 9 || phone.length > 13) {
        Swal.fire({
            icon: 'error',
            text: 'Le numero de telephone doit contenir entre 9 et 13 chiffres',
        });
        return false;
    }

    if (!(/^[0-9]*$/.test(phone))) {
        Swal.fire({
            icon: 'error',
            text: 'Le numero de telephone doit contenir que des chiffres',
        });
        return false;
    }

    if (pw.length < 6){
        Swal.fire({
            icon: 'error',
            text: 'Erreur lors de la soumission du mot de passe actuel',
        });
        return false;
    }

    //on check aussi si l'utilisateur ne veut pas changer son mdp
    if ((newPw.length < 7 || newPw.length > 17) && newPw.length !== 0) {
        Swal.fire({
            icon: 'error',
            text: 'Le nouveau mot de passe doit contenir entre 7 et 17 caractères',
        });
        return false;
    }

    if (newPw !== newPwVerify) {
        Swal.fire({
            icon: 'error',
            text: 'Les nouveaux mots de passe ne correspondent pas',
        });
        return false;
    }


    /*requete ajax pour le formulaire*/
    $.ajax({
        url: '/forms-processing',
        type: 'POST',
        data: {
            'name': name,
            'surname': surname,
            'email': email,
            'phone': phone,
            'pw': pw,
            'newPw': newPw,
            'newPwVerify': newPwVerify,
            'action': 'profileUpdate'
        },
        success: function (res) {
            res = $.parseJSON(res);
            switch (res) {
                case "emailExist":
                    Swal.fire({
                        icon: 'error',
                        text: 'L\'adresse mail est déja utilisée',
                    });
                    break;
                case "phoneExists":
                    Swal.fire({
                        icon: 'error',
                        text: 'Le numéro de téléphone est déja utilisé',
                    });
                    break;
                case "genericFormError":
                    Swal.fire({
                        icon: 'error',
                        text: 'Une erreur est survenue dans la soumission du formulaire. Veuillez reesayer plus tard.',
                    });
                    break;
                case "wrongPassword":
                    Swal.fire({
                        icon: 'error',
                        text: 'Le mot de passe actuel rentré est incorrect',
                    });
                    break;
                case "profileUpdatedSuccesfully":
                    Swal.fire({
                        icon: 'success',
                        text: 'Le profil a été mis a jour avec succes',
                    }).then(function () {
                        window.location.reload();
                    });
                    break;
            }
        },
        error: function (res) {

        }
    });
    return false;
}


/** FUNCTION THAT CHECKS THE LOGIN FORM */
function submitResetForm() {
    let email = $("#email").val();
    let mailRegEx = /^(([^<>()\[\]\.,;:\s@\"]+(\.[^<>()\[\]\.,;:\s@\"]+)*)|(\".+\"))@(([^<>()[\]\.,;:\s@\"]+\.)+[^<>()[\]\.,;:\s@\"]{2,})$/i;
    if (!mailRegEx.test(String(email).toLowerCase()) || !email) {
        Swal.fire({
            icon: 'error',
            text: 'L\'email est invalide',
        });
        return false;
    }

    /*requete ajax pour le formulaire*/
    $.ajax({
        url: '/forms-processing',
        type: 'POST',
        data: {
            'email': email,
            'action': 'forgottenPassword'
        },
        success: function (res) {
            res = $.parseJSON(res);
            if (res === "redirect")
            {
                window.location.href = '/login'; //TODO remove and redirect in php ?
            }

        },
        error: function (res) {

        }
    });

    return false;
}
