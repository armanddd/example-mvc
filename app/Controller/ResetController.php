<?php


namespace App\Controller;

use App\Config\TwigInit;
use App\Services\Session;

class ResetController
{
    public static function renderMainView()
    {
        //#TODO check for session.if logged in, redirect
        Session::getInstance();
        echo TwigInit::loadTwig()->render('reset-form.html.twig');
    }
}