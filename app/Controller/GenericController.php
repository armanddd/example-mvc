<?php


namespace App\Controller;

use App\Config\Database;

class GenericController
{
    public static function getLastLoginAttempt()
    {
        //GETTING THE IP ADRESS
        $ip = isset($_SERVER['HTTP_CLIENT_IP']) ? $_SERVER['HTTP_CLIENT_IP'] : (isset($_SERVER['HTTP_X_FORWARDED_FOR']) ? $_SERVER['HTTP_X_FORWARDED_FOR'] : $_SERVER['REMOTE_ADDR']);
        if (!filter_var($ip, FILTER_VALIDATE_IP)) {
            $ip = $_SERVER['REMOTE_ADDR'];
        }

        //INSTANTIATING DATABASE
        $db = Database::getPdoInstance();

        //SELECT IF THERE IS ALREADY ONE LOGIN ATTEMPT WITH THIS IP ADRESS
        $stmt = $db->prepare("SELECT * FROM login_attempts WHERE ip_adress=:ip_adress");
        $stmt->execute(['ip_adress' => $ip]);
        $lastAttempt = $stmt->fetch(\PDO::FETCH_ASSOC);

        //IF THERE IS NONE, CREATE ONE AND THEN SELECT IT
        if ($lastAttempt)
            return $lastAttempt;
        else {
            $stmt = $db->prepare("INSERT INTO login_attempts (`ip_adress`, `attempt_counter`, `last_attempt`) VALUES (:ip, 0, NOW())");
            $stmt->execute(['ip' => $ip]);
            $stmt = $db->prepare("SELECT * FROM login_attempts WHERE ip_adress=:ip_adress");
            $stmt->execute(['ip_adress' => $ip]);
            $lastAttempt = $stmt->fetch(\PDO::FETCH_ASSOC);
            return $lastAttempt;
        }
    }

}