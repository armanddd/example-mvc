<?php


namespace App\Controller;

use App\Config\TwigInit;
use App\Services\Session;

class ProfileController
{
    public static function renderMainView()
    {
        //INSTANTIATING SESSION
        Session::getInstance();
        //REDIRECTING IF NOT LOGGED IN
        GenericController::redirectIfNotLoggedIn($_SESSION, "");
        echo TwigInit::loadTwig()->render('profile.html.twig', ['session' => $_SESSION]);
    }
}