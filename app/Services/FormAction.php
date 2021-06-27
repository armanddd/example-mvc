<?php

namespace App\Services;

use App\Config\Database;
use App\Controller\GenericController;

class FormAction
{
    /*REGISTER FORM ACTION*/
    public static function registerForm($data)
    {
        extract($data); //ext name, surname, email, phone, pw, pwVerify

        if (isset($name) && !empty($name) && isset($surname) &&
            !empty($surname) && isset($email) && !empty($email) &&
            isset($phone) && !empty($phone) && isset($pw) &&
            !empty($pw) && isset($pwVerify) && !empty($pwVerify)) {

            //instantiate db
            $db = Database::getPdoInstance();

            try {
                /*Checks for existing user based on email*/
                $selectStatement = $db->prepare('SELECT * FROM users WHERE email=:email');
                $selectStatement->execute(['email' => $email]);
                $existingUser = $selectStatement->fetch();
                /*Checks for existing user based on email*/

                if ($existingUser) {
                    echo json_encode("emailExist");
                    return false;
                }
                /*Checks for existing user based on phone*/
                $selectStatement = $db->prepare('SELECT * FROM users WHERE telephone=:telephone');
                $selectStatement->execute(['telephone' => $phone]);
                $existingUser = $selectStatement->fetch();
                /*Checks for existing user based on phone*/
                if ($existingUser) {
                    echo json_encode("phoneExists");
                    return false;
                }

                //REDOING THE JS CHECKS
                if (strlen($name) < 2 || strlen($surname) < 2 || !filter_var($email, FILTER_VALIDATE_EMAIL) ||
                    strlen($phone) < 9 || strlen($phone) > 13 || !is_numeric($phone) || $pw !== $pwVerify) {
                    echo json_encode("genericFormError");
                    return false;
                }

                $statement = $db->prepare('INSERT INTO users (nom, prenom, email, telephone, mdp) 
                                                     VALUES (:nom, :prenom, :email, :telephone, :mdp)');
                $statement->execute([
                    'nom' => $name,
                    'prenom' => $surname,
                    'email' => $email,
                    'telephone' => $phone,
                    'mdp' => password_hash($pw, PASSWORD_ARGON2I)
                ]);

            } catch (\Exception $e) {
                throw $e;
            }
            setcookie("newAccount", "true", time() + 15, "/");
        }
        echo json_encode("accountRegistered");
    }

    /*LOGIN FORM ACTION*/
    public static function loginForm($data)
    {
        extract($data); // ext email, pwd, action, captcha
        $secretCaptcha = "6LchhL8ZAAAAAPet29FTYkA8vNC6jgOcF7F8Das8"; //sercret for google captcha

        $existingLoginAttempt = GenericController::getLastLoginAttempt(); //getting last login attempt by the ip adress

        //CONVERTING STRINGS IN TO DATETIMES
        $lastLoginDate = new \DateTime($existingLoginAttempt['last_attempt']);

        //checks the captcha conditions(if its been more then 2 hours and 10 attempts)
        if (time() - strtotime($lastLoginDate->format("Y-m-d H:i:s")) < 7200 && (int)$existingLoginAttempt['attempt_counter'] >= 10) {
            if (!empty($captcha))
                $verifyCaptcha = json_decode(file_get_contents("https://www.google.com/recaptcha/api/siteverify?secret={$secretCaptcha}&response={$captcha}"));
            if (!isset($verifyCaptcha) || $verifyCaptcha->success === false) {
                echo json_encode("failedCaptcha");
                return false;
            }
        }

        //checks if the form fields have been completed and fetches an user in the db
        if (isset($email) && !empty($email) &&
            isset($pwd) && !empty($pwd)) {
            $db = Database::getPdoInstance();
            try {
                $stmt = $db->prepare("SELECT * FROM users WHERE email=:email");
                $stmt->execute(
                    [
                        'email' => $email,
                    ]
                );
                $user = $stmt->fetch();
            } catch (\Exception $e) {
                throw $e;
            }
            //checks the hashed user password
            if (isset($user['mdp']) && password_verify($pwd, $user['mdp'])) {
                //selecting the user active session in to the database
                Session::getInstance();

                //getting the active subscription for the user if there is one
                $stmt = $db->prepare("SELECT subscriptions_active.id, subscriptions_active.subscription_id, subscriptions.subscription_name, 
                                                subscriptions.subscription_available_workspaces, subscriptions.subscription_price
                                                FROM subscriptions_active 
                                                INNER JOIN subscriptions ON subscriptions_active.subscription_id=subscriptions.id AND subscriptions_active.user_id=:id");
                $stmt->execute(
                    [
                        'id' => $user['id'],
                    ]
                );
                $activeSubscription = $stmt->fetch(\PDO::FETCH_ASSOC);

                //generating the session
                $_SESSION['subscription'] = $activeSubscription;
                $_SESSION['nom'] = $user['nom'];
                $_SESSION['prenom'] = $user['prenom'];
                $_SESSION['email'] = $user['email'];
                $_SESSION['telephone'] = $user['telephone'];
                $_SESSION['user_id'] = $user['id'];

                //tries to fetch the active session
                $stmt = $db->prepare("SELECT * FROM user_active_session WHERE user_id = :user_id");
                $stmt->execute([
                    'user_id' => $user['id']
                ]);

                $activeSession = $stmt->fetch(\PDO::FETCH_ASSOC);

                //if there is no active session, create one
                if ($activeSession === false) {
                    $stmt = $db->prepare("INSERT INTO user_active_session VALUES (default, :user_id, :session_id, NOW())");
                    $stmt->execute([
                        'user_id' => $user['id'],
                        'session_id' => session_id()
                    ]);
                } else { //if there is an active session, update it
                    $stmt = $db->prepare("UPDATE user_active_session SET session_id = :session_id");
                    $stmt->execute([
                        'session_id' => session_id()
                    ]);
                }
                echo json_encode("loginSuccesful");
            } else {
                //FETCHING THE LAST LOGIN ATTEMPT
                $existingLoginAttempt = GenericController::getLastLoginAttempt();

                //checks if $existingLoginAttempt is false.if it is, create a failed login attempt
                if (empty($existingLoginAttempt)) {
                    $stmt = $db->prepare("INSERT INTO login_attempts (`ip_adress`, `attempt_counter`, `last_attempt`) VALUES (:ip, 1, NOW())");
                    $stmt->execute(['ip' => $existingLoginAttempt['ip_adress']]);
                } else { //else if a login attempt already exists with this adress, increments the attempt_counter in db

                    //increment attempt counter by one
                    $stmt = $db->prepare("UPDATE login_attempts 
                                                    SET `attempt_counter` = :attemptCounter , `last_attempt` = NOW()
                                                    WHERE `id` = :loginAttemptID");
                    $stmt->execute(
                        [
                            'attemptCounter' => $existingLoginAttempt['attempt_counter'] + 1,
                            'loginAttemptID' => $existingLoginAttempt['id']
                        ]
                    );
                }
                //checks all the condition for the captcha to exist, then set the login fail json message to true
                if (time() - strtotime($lastLoginDate->format("Y-m-d H:i:s")) < 7200 &&
                    (int)$existingLoginAttempt['attempt_counter'] >= 10 && isset($captcha) && empty($captcha)) {
                    echo json_encode("loginFailWithCaptcha");
                } else { // else if there is no captcha, just set the login fail json message
                    echo json_encode("loginFail");
                    die;
                }
            }
        }
    }

    /*PROFILE UPDATE FORM ACTION*/
    public static function profileForm($data)
    {
        extract($data); //ext name, surname, email, telephone, pw, newPw, newPwVerify, action

        if (isset($name) && !empty($name) && isset($surname) &&
            !empty($surname) && isset($email) && !empty($email) &&
            isset($phone) && !empty($phone) && isset($pw) &&
            !empty($pw)) {
            try {
                //INITIATING SESSION AND DB
                Session::getInstance();
                $db = Database::getPdoInstance();

                /*Checks for existing user based on email*/
                $stmt = $db->prepare('SELECT * FROM users WHERE email=:email');
                $stmt->execute(['email' => $email]);
                $existingUser = $stmt->fetch();
                if ($existingUser && $email !== $_SESSION['email']) { //CHECK SI L'ADRESSE MAIL EST DIFFERENTE DE L'ORIGINALE
                    echo json_encode("emailExist");
                    return false;
                }


                /*Checks for existing user based on phone*/
                $stmt = $db->prepare('SELECT * FROM users WHERE telephone=:telephone');
                $stmt->execute(['telephone' => $phone]);
                $existingUser = $stmt->fetch();
                if ($existingUser && $phone !== $_SESSION['telephone']) {
                    echo json_encode("phoneExists");
                    return false;
                }

                //REDOING THE JS CHECKS
                if (strlen($name) < 2 || strlen($surname) < 2 || !filter_var($email, FILTER_VALIDATE_EMAIL) ||
                    strlen($phone) < 9 || strlen($phone) > 13 || !is_numeric($phone)) {
                    echo json_encode("genericFormError");
                    return false;
                }

                //CHECKING PASSWORD
                $stmt = $db->prepare('SELECT * FROM users WHERE id=:userId');
                $stmt->execute(['userId' => $_SESSION['user_id']]);
                $existingUser = $stmt->fetch(\PDO::FETCH_ASSOC);

                //first of all checks if the entered password is the good one, otherwise returns an error
                if (password_verify($pw, $existingUser['mdp'])) {

                    //then checks if the password needs to be changed
                    if (isset($newPw) && !empty($newPw) && isset($newPwVerify) && !empty($newPwVerify) &&
                        $newPw === $newPwVerify && strlen($newPw) >= 7 && strlen($newPwVerify) <= 17) {

                        //UPDATES THE DATABASE
                        $stmt = $db->prepare("UPDATE users 
                                                        SET `nom` = :nom, `prenom` = :prenom, `email` = :email, `telephone` = :telephone, `mdp` = :password 
                                                        WHERE `id` = :userId");
                        $stmt->execute(
                            [
                                'nom' => $name,
                                'prenom' => $surname,
                                'email' => $email,
                                'telephone' => $phone,
                                'password' => password_hash($newPw, PASSWORD_ARGON2I),
                                'userId' => $_SESSION['user_id']
                            ]
                        );


                        //REGENERATES THE SESSION
                        $_SESSION['nom'] = $name;
                        $_SESSION['prenom'] = $surname;
                        $_SESSION['email'] = $email;
                        $_SESSION['telephone'] = $phone;

                        echo json_encode("profileUpdatedSuccesfully");
                        return false;

                    } else { //IF THE PASSWORD DOESNT NEED TO BE CHANGED, EXECUTES A QUERY WITHOUT THE PASSWORD

                        //UPDATES THE USER IN DATABASE
                        $stmt = $db->prepare("UPDATE users 
                                                        SET `nom` = :nom, `prenom` = :prenom, `email` = :email, `telephone` = :telephone
                                                        WHERE `id` = :userId");
                        $stmt->execute(
                            [
                                'nom' => $name,
                                'prenom' => $surname,
                                'email' => $email,
                                'telephone' => $phone,
                                'userId' => $_SESSION['user_id']
                            ]
                        );


                        //REGENERATES THE SESSION
                        $_SESSION['nom'] = $name;
                        $_SESSION['prenom'] = $surname;
                        $_SESSION['email'] = $email;
                        $_SESSION['telephone'] = $phone;
                    }

                    echo json_encode("profileUpdatedSuccesfully");
                    return false;

                } else { //IF THE WRONG ACTUAL PASSWORD IS ENTERED ON THE FORM RETURNS AN ERROR #TODO also block after 7 attempts
                    echo json_encode("wrongPassword");
                    return false;
                }
            } catch (\Exception $e) {
                throw $e;
            }
        }
    }

    /*FORGOTTEN PASSWORD FORM ACTION*/
    public static function forgottenPassword($data)
    {
        extract($data); // ext email
        if (isset($email) && !empty($email) && filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $db = Database::getPdoInstance();
            //set the cookie  to show the modal after redirection to login
            setcookie("forgottenPasswordValidEmail", "true", time() + 15, "/login.php");
            try {
                $stmt = $db->prepare("SELECT * FROM users WHERE email=:email");
                $stmt->execute(
                    [
                        'email' => $email,
                    ]
                );
                $user = $stmt->fetch();

                if ($user) {
                    //#TODO implement smtp here to send a link to reset password
                }
                echo json_encode("redirect");
            } catch (\Exception $e) {
                throw $e;
            }
        }
    }

}
