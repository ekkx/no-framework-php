<?php

declare(strict_types=1);

namespace Tests\Feature\Route;

use App\Core\Http\Method;
use App\Core\Http\Status;
use PHPUnit\Framework\TestCase;
use Tests\Feature\Route\Helper\TestHelper;

class AuthRouteTest extends TestCase
{
    use TestHelper;

    public function testSignUpPage(): void
    {
        $req = $this->initRequest()->setMethod(Method::GET)->setUri("/auth/signup");

        $res = $this->fetch($req);

        $this->assertEquals(Status::OK, $res->getStatus());
    }

    public function testLoginPage(): void
    {
        $req = $this->initRequest()->setMethod(Method::GET)->setUri("/auth/login");

        $res = $this->fetch($req);

        $this->assertEquals(Status::OK, $res->getStatus());
    }
}
