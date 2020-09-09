<?php

namespace App\Services;

class ReadConfig
{
    public static function readAndExtractConfig(){
        $filePath = __DIR__ . "/../../config.json";
        $file = fopen($filePath, "r");
        $configFile = fread($file, filesize($filePath));
        return json_decode($configFile);
    }
}