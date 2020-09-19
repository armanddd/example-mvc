<?php


namespace App\Controller;

use App\Config\Database;
use App\Config\TwigInit;
use App\Services\Session;

class LoginController
{

    public static function renderMainView()
    {
        //INSTANTIATING SESSION
        Session::getInstance();
        //REDIRECTING IF ALREADY LOGGED IN
        GenericController::redirectIfLoggedIn($_SESSION, "");
        //true if need to show captcha, false anotherwise
        $showCaptcha = self::determineCaptcha();
        //renders view
        echo TwigInit::loadTwig()->render('login-form.html.twig', ["showCaptcha" => $showCaptcha]);
    }

    private static function determineCaptcha(){
        //INSTANTIATING DATABASE
        $db = Database::getPdoInstance();

        //RETRIEVING THE LAST LOGIN ATTEMPT
        $lastLogin = GenericController::getLastLoginAttempt();

        //CONVERTING STRINGS TO DATETIMES
        $lastLoginDate = new \DateTime($lastLogin['last_attempt']);

        //checking if its been more then 10 attempts and less then 2 hours
        if(time() - strtotime($lastLoginDate->format("Y-m-d H:i:s")) < 7200 && (int)$lastLogin['attempt_counter'] >= 10)
        {
            return true;
        } else if (time() - strtotime($lastLoginDate->format("Y-m-d H:i:s")) > 7200){
            // reset the attempt counter if its been more then 2 hours
            $stmt = $db->prepare("UPDATE login_attempts SET `attempt_counter` = 0, `last_attempt` = NOW() WHERE ip_adress=:ip_adress");
            $stmt->execute(['ip_adress' => $lastLogin['ip_adress']]);
        }
        return false;
    }

}