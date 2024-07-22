<?php

declare(strict_types=1);

namespace App\Core;

use App\Core\Exception\MethodNotAllowedException;
use App\Core\Exception\NotFoundException;
use App\Core\Http\Method;
use Closure;
use FastRoute\Dispatcher;
use FastRoute\RouteCollector;

use function FastRoute\simpleDispatcher;

class Router
{
    private static Container $container;
    private static array $routes;
    private static string $prefix;

    public function __construct(Container $container)
    {
        self::$container = $container;
        self::$routes = [];
        self::$prefix = "";
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

    /**
     * @throws NotFoundException
     * @throws MethodNotAllowedException
     */
    public function dispatch(Context $ctx): void
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
                $handler = $routeInfo[1];
                $ctx->req->setParams($routeInfo[2]);

                $handler($ctx);
                break;
        }
    }
}
