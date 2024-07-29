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

        $handlers = [
            ["stream" => "php://stdout", "level" => Level::Info],
            ["stream" => "php://stdout", "level" => Level::Debug],
            ["stream" => "php://stderr", "level" => Level::Error]
        ];

        foreach ($handlers as $handler) {
            $streamHandler = new StreamHandler($handler["stream"], $handler["level"]);
            $streamHandler->setBubble(false);
            $monolog->pushHandler($streamHandler);
        }

        return $monolog;
    }

    public function setLogger(Monolog $monolog): void
    {
        $this->monolog = $monolog;
    }

    private static function buildMessage(string $message, array $extra = [], object|string $from = ""): string
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

    public function info(string $message, array $extra = [], object|string $from = ""): void
    {
        $this->monolog->info(self::buildMessage($message, $extra, $from));
    }

    public function debug(string $message, array $extra = [], object|string $from = ""): void
    {
        $this->monolog->debug(self::buildMessage($message, $extra, $from));
    }

    public function error(string $message, array $extra = [], object|string $from = ""): void
    {
        $this->monolog->error(self::buildMessage($message, $extra, $from));
    }
}
