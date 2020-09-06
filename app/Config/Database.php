<?php

namespace App\Config;

class Database{
    const HOST = '138.68.88.243';
    const DB = 'aioclic';
    const USER = 'aioclic';
    const PASS = '!!Rarara123!!';
    const CHARSET = 'utf8mb4';
    const DSN = 'mysql:host=' . self::HOST . ';' .
    'dbname=' . self::DB  . ';' .
    'charset=' . self::CHARSET;

    public static function getPdoInstance(): \PDO{
        try {
            return new \PDO(self::DSN, self::USER, self::PASS);
        } catch(\PDOException $e) {
            throw $e;
        }
    }
}
