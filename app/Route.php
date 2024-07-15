<?php

declare(strict_types=1);

namespace App;

use App\Controller\HomeController;
use App\Core\Router;

class Route
{
    public static function init(): void
    {
        Router::get("/", HomeController::class, "index");
    }
}
