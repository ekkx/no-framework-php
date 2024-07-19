<?php

declare(strict_types=1);

namespace App\Controller;

use App\Core\Context;
use App\Core\Http\StatusCode;
use Throwable;

class ErrorController
{
    public function notFound(Context $ctx, Throwable $e): void
    {
        $ctx->res->render(StatusCode::NOT_FOUND, "errors/404.twig");
    }

    public function methodNotAllowed(Context $ctx, Throwable $e): void
    {
        $ctx->res->json(StatusCode::METHOD_NOT_ALLOWED, [
            "ok" => false,
            "message" => "method not allowed",
        ]);
    }

    public function internalServerError(Context $ctx, Throwable $e): void
    {
        $ctx->logger->error($e->getMessage(), $this, $e->getTrace());

        $ctx->res->render(StatusCode::INTERNAL_SERVER_ERROR, "errors/500.twig");
    }
}
