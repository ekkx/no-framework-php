<?php

declare(strict_types=1);

namespace App\Controller\Api;

use App\Core\Context;
use App\Core\Exception\Validation\DtoValidationException;
use App\Core\Http\Response;
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

    public function create(Context $ctx): Response
    {
        try {
            $body = new SignupDto($ctx->req->body());

            $user = $this->userService->create($body);

            return $ctx->res->status(Status::CREATED)->json([
                "ok" => true,
                "data" => [
                    "id" => $user->id,
                    "username" => $user->username,
                    "createdAt" => $user->createdAt,
                ],
            ]);
        } catch (DtoValidationException $e) {
            return $ctx->res->status(Status::BAD_REQUEST)->json([
                "ok" => false,
                "message" => $e->getErrors(),
            ]);
        } catch (UserAlreadyRegisteredException $e) {
            return $ctx->res->status(Status::BAD_REQUEST)->json([
                "ok" => false,
                "message" => "user already registered",
            ]);
        }
    }

    public function login(Context $ctx): Response
    {
        try {
            $body = new LoginDto($ctx->req->body());

            $token = $this->userService->login($body);

            return $ctx->res->status(Status::CREATED)->json([
                "ok" => true,
                "accessToken" => $token,
            ]);
        } catch (DtoValidationException $e) {
            return $ctx->res->status(Status::BAD_REQUEST)->json([
                "ok" => false,
                "message" => $e->getErrors(),
            ]);
        } catch (InvalidEmailOrPasswordException $e) {
            return $ctx->res->status(Status::BAD_REQUEST)->json([
                "ok" => false,
                "message" => "invalid email or password",
            ]);
        }
    }
}
