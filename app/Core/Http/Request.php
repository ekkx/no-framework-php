<?php

namespace App\Core\Http;

class Request
{
    private string $method;
    private string $uri;
    private array $params;
    private array $query;
    private array $body;
    private array $headers;
    private array $cookies;
    private array $files;
    private string $ip;

    public function __construct(
        array $_get,
        array $_post,
        array $_cookies,
        array $_files,
        array $_server,
    )
    {
        $this->method = strtoupper($_server["REQUEST_METHOD"]);
        $this->uri = $_server["REQUEST_URI"];
        $this->params = [];
        $this->query = $_get;
        $this->headers = getallheaders();
        $this->cookies = $_cookies;
        $this->files = $_files;
        $this->ip = $_server["REMOTE_ADDR"];

        $json = json_decode(file_get_contents("php://input"), true) ?? [];
        $this->body = empty($_post) ? $json : $_post;
    }

    public static function fromGlobals(): self
    {
        return new self($_GET, $_POST, $_COOKIE, $_FILES, $_SERVER);
    }

    private function getValueFromArray(array $data, ?string $key = null, mixed $default = null): mixed
    {
        if (is_null($key)) {
            return $data;
        }

        return $data[$key] ?? $default;
    }

    public function getMethod(): string
    {
        return $this->method;
    }

    public function getUri(): string
    {
        return $this->uri;
    }

    public function getPath(): string
    {
        return parse_url($this->uri, PHP_URL_PATH);
    }

    public function getParams(?string $key = null, string $default = ""): array|string
    {
        return $this->getValueFromArray($this->params, $key, $default);
    }

    public function getQuery(?string $key = null, string $default = ""): array|string
    {
        return $this->getValueFromArray($this->query, $key, $default);
    }

    public function getBody(?string $key = null, mixed $default = null): mixed
    {
        return $this->getValueFromArray($this->body, $key, $default);
    }

    public function getHeaders(?string $key = null, string $default = ""): array|string
    {
        return $this->getValueFromArray($this->headers, $key, $default);
    }

    public function getCookies(?string $key = null, string $default = ""): array|string
    {
        return $this->getValueFromArray($this->cookies, $key, $default);
    }

    public function getFiles(?string $key = null, string $default = null): array|string|null
    {
        return $this->getValueFromArray($this->files, $key, $default);
    }

    public function getIp(): string
    {
        return $this->ip;
    }

    public function setParams(array $params): self
    {
        $this->params = $params;
        return $this;
    }
}
