<?php

declare(strict_types=1);

namespace App;

class Config
{
    private static Config $instance;

    public bool $appDebug;
    public string $appSecretKey;

    public string $logFileDebug;
    public string $logFileInfo;
    public string $logFileError;

    private function __construct()
    {
        $config = static::getConfig();

        foreach ($config as $key => $value) {
            $this->{$key} = $value;
        }
    }

    private static function getConfig(): array
    {
        return [
            "appDebug" => isset($_ENV["APP_DEBUG"]) && strtolower($_ENV["APP_DEBUG"]) === "true",
            "appSecretKey" => $_ENV["APP_SECRET_KEY"] ?? "",

            "logFileDebug" => $_ENV["LOG_FILE_DEBUG"] ?? "php://stdout",
            "logFileInfo" => $_ENV["LOG_FILE_INFO"] ?? "php://stdout",
            "logFileError" => $_ENV["LOG_FILE_ERROR"] ?? "php://stderr",
        ];
    }

    public static function instance(): Config
    {
        if (!isset(static::$instance)) {
            static::$instance = new static();
        }

        return static::$instance;
    }
}
