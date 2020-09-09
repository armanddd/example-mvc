<?php


namespace App\Controller;

use App\Config\TwigInit;
use App\Services\Session;

class PurchaseController
{
    public static function renderMainView()
    {
        //INSTANTIATING SESSION
        Session::getInstance();
        //REDIRECTING IF NOT LOGGED IN
        GenericController::redirectIfNotLoggedIn($_SESSION, "register");
        echo TwigInit::loadTwig()->render('purchase.html.twig', ['session' => $_SESSION]);
    }
}