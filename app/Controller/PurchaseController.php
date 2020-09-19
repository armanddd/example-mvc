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
        //checks for only one active session at a time
        Session::checkInstance();
        //renders view
        echo TwigInit::loadTwig()->render('purchase.html.twig', ['session' => $_SESSION]);
    }
}