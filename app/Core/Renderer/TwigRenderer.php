<?php

declare(strict_types=1);

namespace App\Core\Renderer;

use App\Core\Exception\RuntimeException;
use Exception;
use Twig\Environment as Twig;

class TwigRenderer implements Renderer
{
    protected Twig $engine;

    public function __construct(Twig $engine)
    {
        $this->engine = $engine;
    }

    public function getEngine(): Twig
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
