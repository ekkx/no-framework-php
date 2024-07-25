<?php

declare(strict_types=1);

namespace App\Controller;

use App\Core\Context;
use App\Core\Http\Response;
use App\Core\Http\Status;

class AuthController
{
    public function signup(Context $ctx): Response
    {
        return $ctx->res->status(Status::OK)->render("auth/signup.twig");
    }

    public function login(Context $ctx): Response
    {
        return $ctx->res->status(Status::OK)->render("auth/login.twig");
    }
}
