<?php

declare(strict_types=1);

namespace App\Core;

use Monolog\Handler\StreamHandler;
use Monolog\Level;
use Monolog\Logger as Monolog;
use ReflectionClass;
use ReflectionException;

class Logger
{
    private Monolog $monolog;

    public function __construct()
    {
        $this->monolog = $this->getDefaultLogger();
    }

    private function getDefaultLogger(): Monolog
    {
        $monolog = new Monolog("App");

        $monolog->pushHandler(new StreamHandler("php://stdout", Level::Info));
        $monolog->pushHandler(new StreamHandler("php://stdout", Level::Debug));
        $monolog->pushHandler(new StreamHandler("php://stderr", Level::Error));

        return $monolog;
    }

    public function setLogger(Monolog $monolog): void
    {
        $this->monolog = $monolog;
    }

    private static function buildMessage(string $message, object|string $from = "", array $extra = []): string
    {
        $prefix = "";
        if ($from) {
            try {
                $prefix = "[" . (new ReflectionClass($from))->getShortName() . "] - ";
            } catch (ReflectionException) {
                if (is_string($from) && strlen($from) > 0) {
                    $prefix = "[" . $from . "] - ";
                }
            }
        }

        if (!empty($extra)) {
            $message .= " " . json_encode($extra, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        }

        return $prefix . $message;
    }

    public function info(string $message, object|string $from = "", array $extra = []): void
    {
        $this->monolog->info(self::buildMessage($message, $from, $extra));
    }

    public function debug(string $message, object|string $from = "", array $extra = []): void
    {
        $this->monolog->debug(self::buildMessage($message, $from, $extra));
    }

    public function error(string $message, object|string $from = "", array $extra = []): void
    {
        $this->monolog->error(self::buildMessage($message, $from, $extra));
    }
}
