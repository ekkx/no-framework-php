<?php

declare(strict_types=1);

namespace App\Core;

use App\Core\Http\Request;
use App\Core\Http\Response;
use App\Core\Renderer\Renderer;
use App\Core\Renderer\TwigRenderer;
use Closure;
use Dotenv\Dotenv;
use Throwable;
use Twig\Environment;
use Twig\Loader\FilesystemLoader;

class Kernel
{
    private Container $container;
    private Router $router;
    private Context $ctx;
    /** @var Middleware[] */
    private array $middlewares;

    public function __construct()
    {
        $this->container = new Container();
        $this->router = new Router($this->container);
        $this->middlewares = [];
        $this->ctx = $this->getDefaultContext();
    }

    private function getDefaultRenderer(): Renderer
    {
        $loader = new FilesystemLoader(__DIR__ . "/../../resources/views");
        $twig = new Environment($loader);
        return new TwigRenderer($twig);
    }

    private function getDefaultContext(): Context
    {
        $req = Request::fromGlobals();
        $res = new Response($this->getDefaultRenderer());

        return new Context($req, $res);
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

    public function template(Renderer $renderer): void
    {
        $this->ctx->res->renderer($renderer);
    }

    public function use(Middleware $middleware): void
    {
        $this->middlewares[] = $middleware;
    }

    public function start(): void
    {
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
