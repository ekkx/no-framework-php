<?php

declare(strict_types=1);

namespace App\Core;

use App\Core\Http\Request;
use App\Core\Http\Response;

readonly class Context
{
    public Request $req;
    public Response $res;
    public Logger $logger;

    public function __construct(Request $req, Response $res, ?Logger $logger = null)
    {
        $this->req = $req;
        $this->res = $res;
        $this->logger = $logger ?? new Logger();
    }
}
