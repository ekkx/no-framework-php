<?php

declare(strict_types=1);

use App\Config;
use App\Core\Kernel;
use App\Core\Renderer\Renderer;
use App\Core\Renderer\TwigRenderer;
use Twig\Environment;
use Twig\Loader\FilesystemLoader;

$root = __DIR__ . "/../";

Kernel::env($root);

$config = Config::instance();

return [
    Config::class => function () use ($config) {
        return $config;
    },
    Renderer::class => function () use ($root) {
        $loader = new FilesystemLoader($root . "/resources/views");
        $twig = new Environment($loader);
        return new TwigRenderer($twig);
    },
];
