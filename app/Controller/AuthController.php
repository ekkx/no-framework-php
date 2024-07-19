<?php

declare(strict_types=1);

namespace App\Controller;

use App\Core\Context;
use App\Core\Http\StatusCode;

class AuthController
{
    public function signup(Context $ctx): void
    {
        $ctx->res->render(StatusCode::OK, "auth/signup.twig");
    }

    public function login(Context $ctx): void
    {
        $ctx->res->render(StatusCode::OK, "auth/login.twig");
    }
}
