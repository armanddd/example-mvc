<?php


namespace App\Controller;

use App\Config\TwigInit;
use App\Services\Session;

class RegisterController
{
    public static function renderMainView()
    {
        //INSTANTIATING SESSION
        Session::getInstance();
        //REDIRECTING IF LOGGED IN
        GenericController::redirectIfLoggedIn($_SESSION, "");
        //renders view
        echo TwigInit::loadTwig()->render('registration-form.html.twig', ['session' => $_SESSION]);
    }
}