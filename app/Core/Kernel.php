<?php

declare(strict_types=1);

namespace App\Core;

use App\Core\Exception\ContainerException;
use App\Core\Http\Request;
use App\Core\Http\Response;
use App\Core\Renderer\Renderer;
use Closure;
use Dotenv\Dotenv;
use Throwable;

class Kernel
{
    private Container $container;
    private Router $router;
    private ?Context $ctx;
    /** @var Middleware[] */
    private array $middlewares;

    public function __construct(?string $envPath = null)
    {
        if (!is_null($envPath)) {
            $this::env($envPath);
        }

        $this->container = new Container();
        $this->router = new Router($this->container);

        $this->ctx = null;
        $this->middlewares = [];
    }

    public function getContainer(): Container
    {
        return $this->container;
    }

    public function getContext(): ?Context
    {
        return $this->ctx;
    }

    /**
     * @param string|string[] $paths
     */
    public static function env(array|string $paths): void
    {
        $dotenv = Dotenv::createImmutable($paths);
        $dotenv->load();
    }

    public function use(Middleware $middleware): void
    {
        $this->middlewares[] = $middleware;
    }

    private function initContext(?Renderer $renderer): Context
    {
        $req = Request::fromGlobals();
        $res = new Response($renderer);

        return new Context($req, $res);
    }

    private function build(): void
    {
        $renderer = null;
        try {
            $renderer = $this->container->make(Renderer::class);
        } catch (ContainerException) {
            // Ignore considering the case where Renderer is unnecessary
        }

        try {
            $this->ctx = $this->container->make(Context::class);
        } catch (ContainerException) {
            $this->ctx = $this->initContext($renderer);
        }
    }

    private function createMiddlewareRunner(): Closure
    {
        return function (Closure $next, Middleware $middleware): Closure {
            return function (Context $ctx) use ($next, $middleware): void {
                try {
                    $middleware::run($next)($ctx);
                } catch (Throwable $e) {
                    $this->router->handleError($ctx, $e);
                }
            };
        };
    }

    private function createAppRunner(): Closure
    {
        return function (Context $ctx): void {
            try {
                $this->router->dispatch($ctx)->send();
            } catch (Throwable $e) {
                $this->router->handleError($this->ctx, $e);
            }
        };
    }

    public function start(): void
    {
        $this->build();

        $pipeline = array_reduce(
            array_reverse($this->middlewares),
            $this->createMiddlewareRunner(),
            $this->createAppRunner(),
        );

        $pipeline($this->ctx);
    }
}
