<?php

declare(strict_types=1);

namespace App\Controller;

use App\Core\Context;
use App\Core\Http\Response;
use App\Core\Http\Status;
use App\Service\UserService;

class AdminController
{
    private UserService $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    public function index(Context $ctx): Response
    {
        $user = $this->userService->findOneById($ctx->req->body("uid"));

        return $ctx->res->status(Status::OK)->render("admin/index.twig", [
            "user" => [
                "username" => $user->username,
                "email" => $user->email,
                "lastLoginAt" => $user->lastLoginAt
            ]
        ]);
    }
}
