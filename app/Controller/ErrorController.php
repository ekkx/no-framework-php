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
        $ctx->res->render(StatusCode::NOT_FOUND, "error/404.twig");
    }

    public function methodNotAllowed(Context $ctx, Throwable $e): void
    {
        $ctx->res->render(StatusCode::NOT_FOUND, "error/405.twig");
    }

    public function internalServerError(Context $ctx, Throwable $e): void
    {
        $ctx->res->render(StatusCode::NOT_FOUND, "error/500.twig");
    }
}
