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
    private static string $outputPathInfo;
    private static string $outputPathDebug;
    private static string $outputPathError;

    public function __construct(Config $config)
    {
        self::$outputPathInfo = $config->logFileInfo;
        self::$outputPathDebug = $config->logFileDebug;
        self::$outputPathError = $config->logFileError;
    }

    public static function run(Closure $next): Closure
    {
        return function (Context $ctx) use ($next) {
            $monolog = new Monolog("App");

            $dateFormat = "Y-m-d H:i:s.v";
            $format = "%datetime% [%level_name%] %message%\n";
            $formatter = new LineFormatter($format, $dateFormat);
            $formatter->includeStacktraces();

            $paths = [
                ["path" => self::$outputPathInfo, "level" => Level::Info],
                ["path" => self::$outputPathDebug, "level" => Level::Debug],
                ["path" => self::$outputPathError, "level" => Level::Error]
            ];

            for ($i = 0; $i < count($paths); $i++) {
                $streamHandler = new StreamHandler($paths[$i]["path"], $paths[$i]["level"]);
                $streamHandler->setFormatter($formatter);
                $monolog->pushHandler($streamHandler);
            }

            $ctx->logger->setLogger($monolog);

            return $next($ctx);
        };
    }
}
