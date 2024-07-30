<?php

declare(strict_types=1);

namespace Tests\Feature\Route\Api;

use App\Core\Http\Status;
use PHPUnit\Framework\TestCase;
use Tests\Feature\Route\Helper\AuthHelper;
use Tests\Feature\Route\Helper\TestHelper;

class UserApiRouteTest extends TestCase
{
    use TestHelper;

    public function testCreateUserAndGetEmail(): string
    {
        $email = $this->randomString(8) . "@example.com";

        $res = (new AuthHelper())->createUser("Jane Doe", $email, "password", "password");

        $this->assertEquals(Status::CREATED, $res->getStatus());

        return $email;
    }

    public function testLogin(): void
    {
        $email = $this->testCreateUserAndGetEmail();

        $res = (new AuthHelper())->login($email, "password");

        $this->assertEquals(Status::CREATED, $res->getStatus());
    }
}
