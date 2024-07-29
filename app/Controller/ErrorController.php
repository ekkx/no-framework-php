<?php

declare(strict_types=1);

namespace App\Controller;

use App\Core\Context;
use App\Core\Http\Response;
use App\Core\Http\Status;
use Throwable;

class ErrorController
{
    public function notFound(Context $ctx, Throwable $e): Response
    {
        return $ctx->res->status(Status::NOT_FOUND)->render("errors/404.twig");
    }

    public function methodNotAllowed(Context $ctx, Throwable $e): Response
    {
        return $ctx->res->status(Status::METHOD_NOT_ALLOWED)->json([
            "ok" => false,
            "message" => "method not allowed",
        ]);
    }

    public function internalServerError(Context $ctx, Throwable $e): Response
    {
        $ctx->logger->error($e->getMessage(), $e->getTrace(), $this);

        return $ctx->res->status(Status::INTERNAL_SERVER_ERROR)->render("errors/500.twig");
    }
}
