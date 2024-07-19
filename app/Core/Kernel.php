<?php

declare(strict_types=1);

namespace App\Core;

use App\Core\Exception\InternalServerErrorException;
use App\Core\Exception\MethodNotAllowedException;
use App\Core\Exception\NotFoundException;
use App\Core\Http\Request;
use App\Core\Http\Response;
use App\Core\Http\StatusCode;
use App\Core\Template\Renderer;
use App\Core\Template\TwigRenderer;
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
    private array $errorHandlers;

    public function __construct()
    {
        $this->container = new Container();
        $this->router = new Router($this->container);
        $this->middlewares = [];
        $this->ctx = $this->getDefaultContext();
        $this->errorHandlers = $this->getDefaultErrorHandlers();
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

    private function getDefaultErrorHandlers(): array
    {
        return [
            NotFoundException::class => function (Context $ctx, Throwable $e) {
                $ctx->res->json(StatusCode::NOT_FOUND, [
                    "code" => StatusCode::NOT_FOUND,
                    "message" => $e->getMessage(),
                ]);
            },
            MethodNotAllowedException::class => function (Context $ctx, Throwable $e) {
                $ctx->res->json(StatusCode::METHOD_NOT_ALLOWED, [
                    "code" => StatusCode::METHOD_NOT_ALLOWED,
                    "message" => $e->getMessage(),
                ]);
            },
            InternalServerErrorException::class => function (Context $ctx, Throwable $e) {
                $ctx->res->json(StatusCode::INTERNAL_SERVER_ERROR, [
                    "code" => StatusCode::INTERNAL_SERVER_ERROR,
                    "message" => $e->getMessage(),
                    "trace" => $e->getTraceAsString(),
                ]);
            },
        ];
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
        $this->ctx->res->setRenderer($renderer);
    }

    public function use(Middleware $middleware): void
    {
        $this->middlewares[] = $middleware;
    }

    public function onError(string $exception, string $class, string $method): void
    {
        $this->errorHandlers[$exception] = function (Context $ctx, Throwable $e) use ($class, $method) {
            $this->container->get($class)->$method($ctx, $e);
        };
    }

    public function start(): void
    {
        try {
            $middlewareRunner = array_reduce(
                array_reverse($this->middlewares),
                function (Closure $next, Middleware $middleware): Closure {
                    return $middleware::run($next);
                },
                function (Context $ctx): void {
                    $this->router->dispatch($ctx);
                }
            );
            $middlewareRunner($this->ctx);
        } catch (Throwable $e) {
            $handler = $this->errorHandlers[get_class($e)] ?? $this->errorHandlers[InternalServerErrorException::class];
            if (!is_null($handler)) {
                call_user_func($handler, $this->ctx, $e);
            }
        }
    }
}
