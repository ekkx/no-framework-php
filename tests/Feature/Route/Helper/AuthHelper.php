<?php

declare(strict_types=1);

namespace Tests\Feature\Route\Helper;

use App\Core\Http\Method;
use App\Core\Http\Response;

class AuthHelper
{
    use TestHelper;

    public function createUser(string $username, string $email, string $password, string $passwordConfirm): Response
    {
        $this->setUp();

        $req = $this->initRequest()->setMethod(Method::POST)->setUri("/api/users/create")->setBody([
            "username" => $username,
            "email" => $email,
            "password" => $password,
            "passwordConfirm" => $passwordConfirm,
        ]);

        return $this->fetch($req);
    }

    public function login(string $email, string $password): Response
    {
        $this->setUp();

        $req = $this->initRequest()->setMethod(Method::POST)->setUri("/api/users/login")->setBody([
            "email" => $email,
            "password" => $password,
        ]);

        return $this->fetch($req);
    }
}
