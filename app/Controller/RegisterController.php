<?php


namespace App\Controller;

use App\Config\TwigInit;
use App\Services\Session;

class RegisterController
{
    public static function renderMainView()
    {
        //#TODO check for session.if logged in, redirect
        Session::getInstance();
        echo TwigInit::loadTwig()->render('registration-form.html.twig', ['session' => $_SESSION]);
    }
}