<?php


namespace App\Controller;

use App\Config\TwigInit;
use App\Services\Session;

class PurchaseController
{
    public static function renderMainView()
    {
        Session::getInstance();
        echo TwigInit::loadTwig()->render('purchase.html.twig', ['session' => $_SESSION]);
    }
}