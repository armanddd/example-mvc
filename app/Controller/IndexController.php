<?php


namespace App\Controller;

use App\Config\TwigInit;
use App\Services\Session;

class IndexController
{
    public static function renderMainView()
    {
        Session::getInstance();
        echo TwigInit::loadTwig()->render('index.html.twig', ['session' => $_SESSION]);
    }
}
