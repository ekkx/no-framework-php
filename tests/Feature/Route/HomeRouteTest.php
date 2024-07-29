<?php

declare(strict_types=1);

namespace Tests\Feature\Route;

use App\Core\Http\Method;
use App\Core\Http\Status;
use PHPUnit\Framework\TestCase;

class HomeRouteTest extends TestCase
{
    use RouteTestHelper;

    public function testIndex(): void
    {
        $req = $this->initRequest()->setMethod(Method::GET)->setUri("/");

        $res = $this->fetch($req);

        $this->assertEquals(Status::OK, $res->getStatus());
    }
}
