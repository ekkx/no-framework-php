<?php

declare(strict_types=1);

namespace App\Controller\Api;

use App\Core\Context;
use App\Core\Exception\ValidationException;
use App\Core\Http\Status;
use App\Dto\LoginDto;
use App\Dto\SignupDto;
use App\Exception\InvalidEmailOrPasswordException;
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
            $body = new SignupDto($ctx->req->body());

            $user = $this->userService->create($body);

            $ctx->res->status(Status::CREATED)->json([
                "ok" => true,
                "data" => [
                    "id" => $user->id,
                    "username" => $user->username,
                    "createdAt" => $user->createdAt,
                ],
            ]);
        } catch (ValidationException $e) {
            $ctx->res->status(Status::BAD_REQUEST)->json([
                "ok" => false,
                "message" => $e->getErrors(),
            ]);
        } catch (UserAlreadyRegisteredException $e) {
            $ctx->res->status(Status::BAD_REQUEST)->json([
                "ok" => false,
                "message" => "user already registered",
            ]);
        }
    }

    public function login(Context $ctx): void
    {
        try {
            $body = new LoginDto($ctx->req->body());

            $token = $this->userService->login($body);

            $ctx->res->status(Status::CREATED)->json([
                "ok" => true,
                "accessToken" => $token,
            ]);
        } catch (ValidationException $e) {
            $ctx->res->status(Status::BAD_REQUEST)->json([
                "ok" => false,
                "message" => $e->getErrors(),
            ]);
        } catch (InvalidEmailOrPasswordException $e) {
            $ctx->res->status(Status::BAD_REQUEST)->json([
                "ok" => false,
                "message" => "invalid email or password",
            ]);
        }
    }
}
