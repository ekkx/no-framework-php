<?php

declare(strict_types=1);

namespace App\Core;

use Closure;

interface Middleware
{
    public static function run(Closure $next): Closure;
}
