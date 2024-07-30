<?php

declare(strict_types=1);

namespace Tests\Feature\Route;

use App\Core\Http\Method;
use App\Core\Http\Status;
use PHPUnit\Framework\TestCase;
use Tests\Feature\Route\Helper\AuthHelper;
use Tests\Feature\Route\Helper\TestHelper;

class AdminRouteTest extends TestCase
{
    use TestHelper;

    public function testIndexPageWithNoAuth(): void
    {
        $req = $this->initRequest()->setMethod(Method::GET)->setUri("/admin");

        $res = $this->fetch($req);

        $this->assertEquals(Status::FOUND, $res->getStatus());
        $this->assertEquals("/auth/login", $res->getRedirectUri());
    }

    public function testIndexPageWithAuth(): void
    {
        $email = $this->randomString(8) . "@example.com";
        $password = "password";

        $authHelper = new AuthHelper();
        $authHelper->createUser("Jane Doe", $email, $password, $password);

        $loginResponse = $authHelper->login($email, $password);
        $token = json_decode($loginResponse->getContent(), true)["accessToken"];

        $req = $this->initRequest()->setMethod(Method::GET)->setUri("/admin")->setCookies(["access-token" => $token]);

        $res = $this->fetch($req);

        $this->assertEquals(Status::OK, $res->getStatus());
    }
}
