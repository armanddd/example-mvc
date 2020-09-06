<?php


namespace App\Controller;

use App\Config\TwigInit;
use App\Services\Session;

class ProfileController
{
    public static function renderMainView()
    {
        //#TODO check for session.if not logged in, redirect
        Session::getInstance();
        echo TwigInit::loadTwig()->render('profile.html.twig', ['session' => $_SESSION]);
    }
}