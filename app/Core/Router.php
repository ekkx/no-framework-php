<?php

declare(strict_types=1);

namespace App\Core;

use App\Core\Exception\Http\InternalServerErrorException;
use App\Core\Exception\Http\MethodNotAllowedException;
use App\Core\Exception\Http\NotFoundException;
use App\Core\Http\Method;
use App\Core\Http\Response;
use App\Core\Http\Status;
use Closure;
use FastRoute\Dispatcher;
use FastRoute\RouteCollector;
use Throwable;
use function FastRoute\simpleDispatcher;

class Router
{
    private static Container $container;
    private static array $routes;
    private static string $prefix;
    private static array $errorHandlers;

    public function __construct(Container $container)
    {
        self::$container = $container;
        self::$routes = [];
        self::$prefix = "";

        self::$errorHandlers = self::getDefaultErrorHandlers();
    }

    private static function getDefaultErrorHandlers(): array
    {
        return [
            NotFoundException::class => function (Context $ctx, Throwable $e) {
                $ctx->res->status(Status::NOT_FOUND)->json([
                    "code" => Status::NOT_FOUND,
                    "message" => $e->getMessage(),
                ]);
            },
            MethodNotAllowedException::class => function (Context $ctx, Throwable $e) {
                $ctx->res->status(Status::METHOD_NOT_ALLOWED)->json([
                    "code" => Status::METHOD_NOT_ALLOWED,
                    "message" => $e->getMessage(),
                ]);
            },
            InternalServerErrorException::class => function (Context $ctx, Throwable $e) {
                $ctx->res->status(Status::INTERNAL_SERVER_ERROR)->json([
                    "code" => Status::INTERNAL_SERVER_ERROR,
                    "message" => $e->getMessage(),
                ]);
            },
        ];
    }

    public static function group(string $prefix, Closure $handler): void
    {
        $previousPrefix = self::$prefix;
        self::$prefix .= $prefix;
        $handler();
        self::$prefix = $previousPrefix;
    }

    private static function add(string $method, string $path, Closure $handler): void
    {
        $route = self::$prefix . $path;
        if ($route !== "/") {
            $route = rtrim($route, "/");
        }

        self::$routes[] = [$method, $route, $handler];
    }

    public static function get(string $path, string $class, string $method): void
    {
        self::add(Method::GET, $path, function (Context $ctx) use ($class, $method) {
            self::$container->get($class)->$method($ctx);
        });
    }

    public static function post(string $path, string $class, string $method): void
    {
        self::add(Method::POST, $path, function (Context $ctx) use ($class, $method) {
            self::$container->get($class)->$method($ctx);
        });
    }

    public static function put(string $path, string $class, string $method): void
    {
        self::add(Method::PUT, $path, function (Context $ctx) use ($class, $method) {
            self::$container->get($class)->$method($ctx);
        });
    }

    public static function delete(string $path, string $class, string $method): void
    {
        self::add(Method::DELETE, $path, function (Context $ctx) use ($class, $method) {
            self::$container->get($class)->$method($ctx);
        });
    }

    public static function onError(string $exception, string $class, string $method): void
    {
        self::$errorHandlers[$exception] = function (Context $ctx, Throwable $e) use ($class, $method) {
            self::$container->get($class)->$method($ctx, $e);
        };
    }

    public function handleError(Context $ctx, Throwable $e): void
    {
        $handler = self::$errorHandlers[get_class($e)] ?? self::$errorHandlers[InternalServerErrorException::class];
        if (!is_null($handler)) {
            call_user_func($handler, $ctx, $e);
        }
    }

    /**
     * @throws NotFoundException
     * @throws MethodNotAllowedException
     */
    public function dispatch(Context $ctx): Response
    {
        $dispatcher = simpleDispatcher(function (RouteCollector $r) {
            foreach (self::$routes as $route) {
                $r->addRoute($route[0], $route[1], $route[2]);
            }
        });

        $uri = $ctx->req->uri();
        if (false !== $pos = strpos($uri, "?")) {
            $uri = substr($uri, 0, $pos);
        }
        $uri = rawurldecode($uri);

        $routeInfo = $dispatcher->dispatch($ctx->req->method(), $uri);
        switch ($routeInfo[0]) {
            case Dispatcher::NOT_FOUND:
                throw new NotFoundException("Route not found");
            case Dispatcher::METHOD_NOT_ALLOWED:
                throw new MethodNotAllowedException("Method not allowed");
            case Dispatcher::FOUND:
                $ctx->req->setParams($routeInfo[2]);
                $handler = $routeInfo[1];
                $handler($ctx);
                return $ctx->res;
        }

        return $ctx->res;
    }
}
