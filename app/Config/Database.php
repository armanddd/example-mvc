<?php

namespace App\Config;

use App\Services\ReadConfig;

class Database{
    public static function getPdoInstance(): \PDO{
        try {
            $config = ReadConfig::readAndExtractConfig();
            $dsn = 'mysql:host=' . $config->dbHost . ';' . 'dbname=' . $config->dbName  . ';' . 'charset=' . $config->dbCharset;
            return new \PDO($dsn, $config->dbUsername, $config->dbPassword);
        } catch(\PDOException $e) {
            throw $e;
        }
    }
}
