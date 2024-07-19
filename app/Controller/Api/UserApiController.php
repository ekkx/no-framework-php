<?php

declare(strict_types=1);

namespace App\Controller\Api;

use App\Core\Context;
use App\Core\Exception\ValidationException;
use App\Core\Http\StatusCode;
use App\Dto\SignupDto;
use App\Exception\UserAlreadyRegisteredException;
use App\Service\UserService;

class UserApiController
{
    private UserService $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    public function create(Context $ctx): void
    {
        try {
            $body = new SignupDto($ctx->req->getBody());

            $user = $this->userService->create($body->username, $body->email, $body->password);

            $ctx->res->json(StatusCode::CREATED, [
                "ok" => true,
                "data" => [
                    "id" => $user->id,
                    "username" => $user->username,
                    "createdAt" => $user->createdAt,
                ],
            ]);
        } catch (ValidationException $e) {
            $ctx->res->json(StatusCode::BAD_REQUEST, [
                "ok" => false,
                "message" => $e->getErrors(),
            ]);
        } catch (UserAlreadyRegisteredException $e) {
            $ctx->res->json(StatusCode::BAD_REQUEST, [
                "ok" => false,
                "message" => "user already registered",
            ]);
        }
    }

    public function login(Context $ctx): void
    {

    }

    public function logout(Context $ctx): void
    {
        $ctx->res->redirect("/auth/login");
    }
}
