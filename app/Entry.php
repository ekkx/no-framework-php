<?php

declare(strict_types=1);

namespace App;

use App\Core\Kernel;
use App\Middleware\AuthMiddleware;
use App\Middleware\CustomLoggerMiddleware;
use App\Middleware\RequestLoggerMiddleware;

class Entry
{
    public static function run(): void
    {
        $root = __DIR__ . "/../";

        $app = new Kernel($root);

        $container = $app->getContainer();
        $container->inject(require $root . "/config/providers.php");

        $config = Config::instance();

        $app->use(new CustomLoggerMiddleware($config));
        $app->use(new RequestLoggerMiddleware());
        $app->use(new AuthMiddleware($config));

        Route::init();

        $app->start();
    }
}
