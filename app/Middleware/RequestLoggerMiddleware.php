<?php

declare(strict_types=1);

namespace App\Middleware;

use App\Core\Context;
use App\Core\Middleware;
use Closure;

class RequestLoggerMiddleware implements Middleware
{
    public static function run($next): Closure
    {
        return function (Context $ctx) use ($next) {
            $start = microtime(true); // Measure processing time

            $next($ctx);

            // After actions

            $finish = microtime(true);
            $time = floor(($finish - $start) * 1000);

            $method = $ctx->req->getMethod();
            $uri = $ctx->req->getUri();
            $code = $ctx->res->getStatusCode();
            $body = json_encode($ctx->req->getBody(), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

            $ctx->logger->debug("{$method}: {$uri} ({$code}) +{$time}ms {$body}", "HTTP");
        };
    }
}
