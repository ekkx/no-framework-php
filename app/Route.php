<?php

declare(strict_types=1);

namespace App;

use App\Controller\ErrorController;
use App\Controller\HomeController;
use App\Core\Exception\InternalServerErrorException;
use App\Core\Exception\MethodNotAllowedException;
use App\Core\Exception\NotFoundException;
use App\Core\Kernel;
use App\Core\Router;

class Route
{
    public static function init(Kernel $app): void
    {
        Router::get("/", HomeController::class, "index");

        $app->onError(NotFoundException::class, ErrorController::class, "notFound");
        $app->onError(MethodNotAllowedException::class, ErrorController::class, "methodNotAllowed");
        $app->onError(InternalServerErrorException::class, ErrorController::class, "internalServerError");
    }
}
