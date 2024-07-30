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
        $app = new Kernel();

        $container = $app->getContainer();
        $container->inject(require __DIR__ . "/../config/providers.php");

        $config = Config::instance();

        $app->use(new CustomLoggerMiddleware($config));
        $app->use(new RequestLoggerMiddleware());
        $app->use(new AuthMiddleware($config));

        Route::init();

        $app->start();
    }
}
