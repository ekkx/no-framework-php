<?php

declare(strict_types=1);

namespace Tests\Feature\Route;

use App\Core\Context;
use App\Core\Http\Method;
use App\Core\Http\Request;
use App\Core\Http\Response;
use App\Core\Kernel;
use App\Core\Renderer\Renderer;
use App\Route;

trait RouteTestHelper
{
    protected string $root = __DIR__ . "/../../../";
    protected array $dependencies;

    public function setUp(): void
    {
        $this->dependencies = require $this->root . "/config/providers.php";
    }

    protected function initRequest(): Request
    {
        return new Request([], [], [], [], [
            "REQUEST_METHOD" => Method::GET,
            "REQUEST_URI" => "/",
            "REMOTE_ADDR" => "127.0.0.1",
        ]);
    }

    protected function initDependencies(Request $req): array
    {
        return array_merge($this->dependencies, [
            Context::class => function () use ($req) {
                $renderer = $this->dependencies[Renderer::class]();
                return new Context($req, new Response($renderer));
            }
        ]);
    }

    protected function fetch(Request $req): Response
    {
        $app = new Kernel($this->root);

        $container = $app->getContainer();
        $container->inject($this->initDependencies($req));

        Route::init();

        $app->start();

        return $app->getContext()->res;
    }
}
