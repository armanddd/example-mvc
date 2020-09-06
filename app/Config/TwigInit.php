<?php

namespace App\Config;

class TwigInit
{
    private static $twig = NULL;

    static function loadTwig()
    {
        // Specify our Twig templates location
        $loader = new \Twig\Loader\FilesystemLoader(__DIR__ . '/../../assets/templates');

        // Instantiate our Twig
        self::$twig = new \Twig\Environment($loader, ['debug' => true]);
        self::$twig->addExtension(new \Twig\Extension\DebugExtension()); #TODO supp debug avant mise en prod
        self::$twig->addGlobal('session', $_SESSION);

        self::$twig->addFunction(new \Twig\TwigFunction('asset', function ($asset) {
            // implement whatever logic you need to determine the asset path
            return sprintf('/%s', ltrim($asset, '/'));
        }));
        return self::$twig;
    }
}
