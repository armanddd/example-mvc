<?php


namespace App\Controller;

use App\Config\TwigInit;
use App\Services\Session;

class ResetController
{
    public static function renderMainView()
    {
        //INSTANTIATING SESSION
        Session::getInstance();
        //REDIRECTING IF ALREADY LOGGED IN
        GenericController::redirectIfLoggedIn($_SESSION, "");
        echo TwigInit::loadTwig()->render('reset-form.html.twig');
    }
}