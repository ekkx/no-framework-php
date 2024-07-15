<?php

declare(strict_types=1);

namespace App\Core;

use App\Core\Exception\InternalServerErrorException;
use App\Core\Exception\MethodNotAllowedException;
use App\Core\Exception\NotFoundException;
use App\Core\Http\Request;
use App\Core\Http\Response;
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
    private array $errorHandlers;

    public function __construct()
    {
        $this->container = new Container();
            $this->router = new Router($this->container);
        $this->ctx = $this->getDefaultContext();
        $this->middlewares = [];

        $this->errorHandlers = [
//            NotFoundException::class => function () {
//                echo "Not Found";
//            },
//            MethodNotAllowedException::class => function () {
//                echo "Method Not Allowed";
//            },
            InternalServerErrorException::class => function (Context $ctx, Throwable $e) {
                echo $e->getMessage();
//                echo "Internal Server Error";
            },
        ];
    }

    private function getDefaultContext(): Context
    {
        $req = Request::fromGlobals();
        $res = new Response();

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
