<?php

declare(strict_types=1);

namespace App\Controller;

use App\Core\Context;
use App\Core\Http\Status;
use App\Service\HomeService;

class HomeController
{
    private HomeService $homeService;

    public function __construct(HomeService $homeService)
    {
        $this->homeService = $homeService;
    }

    public function index(Context $ctx): void
    {
        $message = $this->homeService->getHello();

        $ctx->res->status(Status::OK)->render("index.twig", [
            "message" => $message,
        ]);
    }
}
