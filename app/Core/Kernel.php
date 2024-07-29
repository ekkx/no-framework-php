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
    private Context $ctx;
    /** @var Middleware[] */
    private array $middlewares;

    public function __construct(?string $envPath = null)
    {
        if (!is_null($envPath)) {
            $this::env($envPath);
        }

        $this->container = new Container();
        $this->router = new Router($this->container);
        $this->middlewares = [];
    }

    private function initialize(): void
    {
        $renderer = null;
        try {
            $renderer = $this->container->make(Renderer::class);
        } catch (ContainerException) {
            // Ignore considering the case where Renderer is unnecessary
        }

        $req = Request::fromGlobals();
        $res = new Response($renderer);

        $this->ctx = new Context($req, $res);
    }

    public function getContainer(): Container
    {
        return $this->container;
    }

    public function getContext(): Context
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

    public function start(): void
    {
        $this->initialize();

        try {
            $runner = array_reduce(
                array_reverse($this->middlewares),
                function (Closure $next, Middleware $middleware): Closure {
                    return $middleware::run($next);
                },
                function (Context $ctx): void {
                    $this->router->dispatch($ctx)->send();
                }
            );
            $runner($this->ctx);
        } catch (Throwable $e) {
            $this->router->handleError($this->ctx, $e);
        }
    }
}
