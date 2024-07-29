<?php

declare(strict_types=1);

use App\Config;
use App\Core\Renderer\Renderer;
use App\Core\Renderer\TwigRenderer;
use Twig\Environment;
use Twig\Loader\FilesystemLoader;

$config = Config::instance();

return [
    Config::class => function () use ($config) {
        return $config;
    },
    Renderer::class => function () use ($config) {
        $loader = new FilesystemLoader(__DIR__ . "/../resources/views");
        $twig = new Environment($loader);
        return new TwigRenderer($twig);
    },
];
