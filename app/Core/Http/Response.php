<?php

declare(strict_types=1);

namespace App\Core\Http;

use App\Core\Renderer\Renderer;

class Response
{
    private ?Renderer $renderer;
    private int $status;
    private array $headers;
    private ?string $content;
    private ?string $redirectUri;

    public function __construct(?Renderer $renderer = null)
    {
        $this->status = Status::OK;
        $this->headers = ["Content-Type" => ContentType::TEXT_HTML];
        $this->content = null;
        $this->redirectUri = null;

        $this->renderer = $renderer;
    }

    public function getRenderer(): ?Renderer
    {
        return $this->renderer;
    }

    public function getStatus(): int
    {
        return $this->status;
    }

    public function getHeaders(?string $key = null, mixed $default = ""): mixed
    {
        if (is_null($key)) {
            return $this->headers;
        }

        return $this->headers[$key] ?? $default;
    }

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function getRedirectUri(): ?string
    {
        return $this->redirectUri;
    }

    public function renderer(Renderer $renderer): void
    {
        $this->renderer = $renderer;

    }

    public function status(int $status): self
    {
        $this->status = $status;

        return $this;
    }

    public function headers(array $headers): self
    {
        $this->headers = array_merge($this->headers, $headers);

        return $this;
    }

    public function content(?string $content): self
    {
        $this->content = $content;

        return $this;
    }

    public function send(): self
    {
        if (headers_sent()) {
            return $this;
        }

        http_response_code($this->status);
        foreach ($this->headers as $header => $value) {
            header($header . ": " . $value, false);
        }

        echo $this->content;

        return $this;
    }

    public function redirect(string $url, int $status = 302): self
    {
        $this->redirectUri = $url;

        return $this->status($status)->headers(["Location" => $this->redirectUri])->send();
    }

    public function json(array $data): self
    {
        $content = json_encode($data, JSON_UNESCAPED_UNICODE, JSON_UNESCAPED_SLASHES);

        return $this->headers(["Content-Type" => ContentType::APPLICATION_JSON])->content($content)->send();
    }

    public function render(string $view, array $data = []): self
    {
        if (is_null($this->renderer)) {
            $this->status(Status::INTERNAL_SERVER_ERROR)->json([
                "code" => Status::INTERNAL_SERVER_ERROR,
                "message" => "No renderer configured",
            ]);

            return $this;
        }

        $content = $this->renderer->render($view, $data);

        return $this->headers(["Content-Type" => ContentType::TEXT_HTML])->content($content)->send();
    }
}
