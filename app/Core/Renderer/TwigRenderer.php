<?php

declare(strict_types=1);

namespace App\Core\Renderer;

use App\Core\Exception\RuntimeException;
use Exception;
use Twig\Environment as TwigEngine;

class TwigRenderer implements Renderer
{
    protected TwigEngine $engine;

    public function __construct(TwigEngine $engine)
    {
        $this->engine = $engine;
    }

    public function getEngine(): TwigEngine
    {
        return $this->engine;
    }

    /**
     * @throws RuntimeException
     */
    public function render(string $template, array $data = []): string
    {
        try {
            return $this->engine->render($template, $data);
        } catch (Exception $e) {
            throw new RuntimeException($e->getMessage());
        }
    }
}
