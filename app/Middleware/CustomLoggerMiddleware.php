<?php

declare(strict_types=1);

namespace App\Middleware;

use App\Config;
use App\Core\Context;
use App\Core\Middleware;
use Closure;
use Monolog\Formatter\LineFormatter;
use Monolog\Handler\StreamHandler;
use Monolog\Level;
use Monolog\Logger as Monolog;

class CustomLoggerMiddleware implements Middleware
{
    private static Config $config;

    public function __construct(Config $config)
    {
        self::$config = $config;
    }

    public static function run(Closure $next): Closure
    {
        return function (Context $ctx) use ($next) {
            $monolog = new Monolog("App");

            $dateFormat = "Y-m-d H:i:s.v";
            $format = "%datetime% [%level_name%] %message%\n";
            $formatter = new LineFormatter($format, $dateFormat);
            $formatter->includeStacktraces();

            $handlers = [
                ["stream" => self::$config->logFileInfo, "level" => Level::Info],
                ["stream" => self::$config->logFileDebug, "level" => Level::Debug],
                ["stream" => self::$config->logFileError, "level" => Level::Error]
            ];

            foreach ($handlers as $handler) {
                // Skip handler if its level is lower than the configured log level
                if (Level::fromName(self::$config->logLevel)->value > $handler["level"]->value) {
                    continue;
                }

                $streamHandler = new StreamHandler($handler["stream"], $handler["level"]);
                $streamHandler->setFormatter($formatter);
                $streamHandler->setBubble(false);
                $monolog->pushHandler($streamHandler);
            }

            $ctx->logger->setLogger($monolog);

            return $next($ctx);
        };
    }
}
