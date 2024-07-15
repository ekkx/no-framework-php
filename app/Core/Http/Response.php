<?php

declare(strict_types=1);

namespace App\Core\Http;

class Response
{
    private int $statusCode;
    private array $headers;
    private bool $responded;

    public function __construct()
    {
        $this->withStatusCode(StatusCode::OK);
        $this->withHeader("Content-Type", ContentType::TEXT_HTML);

        $this->responded = false;
    }

    public function getStatusCode(): int
    {
        return $this->statusCode;
    }

    public function getHeaders(): array
    {
        return $this->headers;
    }

    public function withStatusCode(int $statusCode): self
    {
        $this->statusCode = $statusCode;
        return $this;
    }

    public function withHeader(string $name, string $value): self
    {
        $this->headers[$name] = $value;
        return $this;
    }

    public function withHeaders(array $headers): self
    {
        $this->headers = array_merge($this->headers, $headers);
        return $this;
    }

    private function respond(?string $content = null): void
    {
        if ($this->responded) {
            return;
        }

        http_response_code($this->statusCode);
        foreach ($this->headers as $header => $value) {
            header($header . ": " . $value, false);
        }

        if (!is_null($content)) {
            echo $content;
        }

        $this->responded = true;
    }

    public function redirect(string $url): void
    {
        $this->withStatusCode(StatusCode::FOUND)->withHeader("Location", $url)->respond();
    }

    public function json(int $statusCode, array $data): void
    {
        $content = json_encode($data, JSON_UNESCAPED_UNICODE, JSON_UNESCAPED_SLASHES);
        $this->withStatusCode($statusCode)->withHeader("Content-Type", ContentType::APPLICATION_JSON)->respond($content);
    }
}
