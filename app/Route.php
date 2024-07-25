<?php

declare(strict_types=1);

namespace App;

use App\Controller\AdminController;
use App\Controller\Api\UserApiController;
use App\Controller\AuthController;
use App\Controller\ErrorController;
use App\Controller\HomeController;
use App\Core\Exception\Http\InternalServerErrorException;
use App\Core\Exception\Http\MethodNotAllowedException;
use App\Core\Exception\Http\NotFoundException;
use App\Core\Router;

class Route
{
    public static function init(): void
    {
        Router::get("/", HomeController::class, "index");

        Router::group("/auth", function () {
            Router::get("/signup", AuthController::class, "signup");
            Router::get("/login", AuthController::class, "login");
        });

        Router::get("/admin", AdminController::class, "index");

        Router::group("/api", function () {
            Router::group("/users", function () {
                Router::post("/login", UserApiController::class, "login");
                Router::post("/create", UserApiController::class, "create");
            });
        });

        Router::onError(NotFoundException::class, ErrorController::class, "notFound");
        Router::onError(MethodNotAllowedException::class, ErrorController::class, "methodNotAllowed");
        Router::onError(InternalServerErrorException::class, ErrorController::class, "internalServerError");
    }
}
