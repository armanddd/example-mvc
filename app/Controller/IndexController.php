<?php


namespace App\Controller;

use App\Config\TwigInit;
use App\Services\Session;

class IndexController
{
    public static function renderMainView()
    {
        //gets user instance
        Session::getInstance();
        //checks for only one active session at a time
        Session::checkInstance();
        //renders view
        echo TwigInit::loadTwig()->render('index.html.twig', ['session' => $_SESSION]);
    }
}
