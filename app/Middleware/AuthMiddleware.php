<?php

declare(strict_types=1);

namespace App\Middleware;

use App\Config;
use App\Core\Context;
use App\Core\Middleware;
use Closure;
use Exception;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class AuthMiddleware implements Middleware
{
    private static string $secretKey;
    private static array $triggerPaths = [
        "/admin",
    ];

    public function __construct(Config $config)
    {
        self::$secretKey = $config->appSecretKey;
    }

    public static function run(Closure $next): Closure
    {
        return function (Context $ctx) use ($next) {
            if (!in_array($ctx->req->path(), self::$triggerPaths, true)) {
                return $next($ctx);
            }

            $ctx->logger->debug("Verifying token...", [], self::class);

            try {
                $token = $ctx->req->cookies("access_token");
                $key = new Key(self::$secretKey, "HS256");

                $payload = JWT::decode($token, $key);

                $ctx->req->setBody(["uid" => intval($payload->uid)]);
            } catch (Exception $e) {
                $ctx->logger->debug($e->getMessage(), [], self::class);
                $ctx->res->redirect("/auth/login");

                return null; // stop running middlewares
            }

            $ctx->logger->debug("Verification success!", [], self::class);

            return $next($ctx);
        };
    }
}
