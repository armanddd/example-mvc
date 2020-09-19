<?php

namespace App\Services;

use App\Config\Database;

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

    /**
     * Check si il y a deja une session de l'utilisateur en place
     */
    public static function checkInstance()
    {
        //checking if user is logged in
        if (isset($_SESSION['user_id']) && !empty($_SESSION['user_id']))
        {
            //retrieving actual session id
            $actualSessionId = session_id();

            //instantiating database
            $db = Database::getPdoInstance();

            $stmt = $db->prepare("SELECT * FROM user_active_session WHERE user_id = :user_id LIMIT 1");
            $stmt->execute(["user_id" => $_SESSION['user_id']]);
            $row = $stmt->fetch(\PDO::FETCH_ASSOC);

            //checks if the last session id from the database is equal to the actual session id
            if ($row['session_id'] !== $actualSessionId)
            {
                setcookie("anotherLoginDetected", "true", time() + 15, "/");
                self::logout();
            }
        }
    }

    public static function logout(): void
    {
        Session::getInstance();
        session_unset();
        session_destroy();
        session_write_close();
        setcookie(session_name(), '', 0, '/');

        header(header("Location: http://" . $_SERVER['HTTP_HOST']));
    }
}