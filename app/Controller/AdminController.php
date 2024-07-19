<?php

declare(strict_types=1);

namespace App\Controller;

use App\Core\Context;
use App\Core\Http\StatusCode;

class AdminController
{
    public function index(Context $ctx): void
    {
        $ctx->res->render(StatusCode::OK, "admin/index.twig", [
            "user" => [
                "username" => "Username",
                "email" => "your@email.com",
                "password" => "*********"
            ]
        ]);
    }
}
