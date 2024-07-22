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

    public function method(): string
    {
        return $this->method;
    }

    public function uri(): string
    {
        return $this->uri;
    }

    public function path(): string
    {
        return parse_url($this->uri, PHP_URL_PATH);
    }

    public function params(?string $key = null, mixed $default = ""): mixed
    {
        return $this->getValueFromArray($this->params, $key, $default);
    }

    public function query(?string $key = null, mixed $default = ""): mixed
    {
        return $this->getValueFromArray($this->query, $key, $default);
    }

    public function body(?string $key = null, mixed $default = null): mixed
    {
        return $this->getValueFromArray($this->body, $key, $default);
    }

    public function headers(?string $key = null, mixed $default = ""): mixed
    {
        return $this->getValueFromArray($this->headers, $key, $default);
    }

    public function cookies(?string $key = null, mixed $default = ""): mixed
    {
        return $this->getValueFromArray($this->cookies, $key, $default);
    }

    public function files(?string $key = null, mixed $default = null): mixed
    {
        return $this->getValueFromArray($this->files, $key, $default);
    }

    public function ip(): string
    {
        return $this->ip;
    }

    public function setParams(array $params): self
    {
        $this->params = array_merge($this->params ?? [], $params);
        return $this;
    }

    public function setQuery(array $query): self
    {
        $this->query = array_merge($this->query ?? [], $query);
        return $this;
    }

    public function setBody(array $body): self
    {
        $this->body = array_merge($this->body ?? [], $body);
        return $this;
    }

    public function setHeaders(array $headers): self
    {
        $this->headers = array_merge($this->headers ?? [], $headers);
        return $this;
    }
}
