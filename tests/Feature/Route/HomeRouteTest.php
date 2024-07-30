<?php

declare(strict_types=1);

namespace Tests\Feature\Route;

use App\Core\Http\Method;
use App\Core\Http\Status;
use PHPUnit\Framework\TestCase;
use Tests\Feature\Route\Helper\TestHelper;

class HomeRouteTest extends TestCase
{
    use TestHelper;

    public function testIndexPage(): void
    {
        $req = $this->initRequest()->setMethod(Method::GET)->setUri("/");

        $res = $this->fetch($req);

        $this->assertEquals(Status::OK, $res->getStatus());
    }
}
