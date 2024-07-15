<?php

declare(strict_types=1);

namespace App;

use App\Core\Kernel;

class Entry
{
    public static function run(): void
    {
        $app = new Kernel();

        Route::init();

        $app->start();
    }
}
