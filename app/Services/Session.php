<?php

namespace App\Services;
/**
 * Singleton Session
 */
class Session
{
    private static $_instance = null;

    /**
     * Constructeur privé pour empêcher une instantiation de Session
     * depuis l'extérieur de la classe
     */
    private function __construct()
    {
        session_start();
    }

    /**
     * A la place d'un constructeur public pour instancier depuis l'extérieur,
     * on expose une méthode statique getInstance qui se chargera elle-même de
     * créer une instance et de la retourner.
     * Du coup, si on appelle plusieurs fois getInstance, on retournera toujours la même instance de Session
     *
     * @return Session|null
     */
    public static function getInstance(): ?Session
    {
        if (self::$_instance == null) {
            self::$_instance = new Session();
        }

        return self::$_instance;
    }

    public static function logout(): void
    {
        Session::getInstance();
        session_unset();
        session_destroy();
        session_write_close();
        setcookie(session_name(),'',0,'/');
        header(header("Location: http://" . $_SERVER['HTTP_HOST']));
    }
}