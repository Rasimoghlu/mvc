<?php

namespace Src\Handlers;

use App\Interfaces\RequestInterface;

class RequestHandler implements RequestInterface
{
    private string $scriptName;
    private string $baseUrl;
    private string $url;
    private string $fullUrl;
    private string $queryString;

    public function __construct()
    {
        $this->checkCsrfToken();
    }

    private function checkCsrfToken(): void
    {
        if (strtolower($_SERVER['REQUEST_METHOD'] ?? 'GET') !== 'post') {
            return;
        }

        if (!isset($_POST['_token']) || !isset($_SESSION['_token'])) {
            http_response_code(403);
            echo '403 Forbidden: CSRF token missing.';
            exit;
        }

        if (!hash_equals($_SESSION['_token'], $_POST['_token'])) {
            http_response_code(403);
            echo '403 Forbidden: CSRF token mismatch.';
            exit;
        }
    }

    public function handle(): void
    {
        $this->scriptName = str_replace('\\', '', dirname($_SERVER['SCRIPT_NAME']));
        $this->setBaseUrl();
        $this->setUrl();
    }

    public function setBaseUrl(): string
    {
        $isSecure = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
            || (isset($_SERVER['SERVER_PORT']) && $_SERVER['SERVER_PORT'] == 443);

        $protocol = $isSecure ? 'https://' : 'http://';
        $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
        $scriptName = $this->scriptName ?? '';

        return $this->baseUrl = $protocol . $host . $scriptName;
    }

    public function setUrl(): void
    {
        $requestUri = urldecode($_SERVER['REQUEST_URI']);
        $requestUri = rtrim(preg_replace("#^" . $this->scriptName . '#', '', $requestUri), '/');

        $queryString = '';

        $this->fullUrl = $requestUri;

        if (str_contains($requestUri, '?')) {
            [$requestUri, $queryString] = explode('?', $requestUri);
        }

        $this->url = $requestUri;
        $this->queryString = $queryString;
    }

    public function baseUrl(): string
    {
        return $this->baseUrl;
    }

    public function url(): string
    {
        return $this->url;
    }

    public function queryString(): string
    {
        return $this->queryString;
    }

    public function fullUrl(): string
    {
        return $this->fullUrl;
    }

    public function method(): string
    {
        return $_SERVER['REQUEST_METHOD'];
    }

    public function has(array $type, string $key): bool
    {
        return array_key_exists($key, $type);
    }

    public function value(string $key, ?array $type = null): mixed
    {
        $type = $type ?? $_REQUEST;

        return $this->has($type, $key) ? $type[$key] : null;
    }

    public function get(string $key): mixed
    {
        return $this->value($key, $_GET);
    }

    public function post(string $key): mixed
    {
        return $this->value($key, $_POST);
    }

    public function set(string $key, mixed $value): mixed
    {
        $_REQUEST[$key] = $value;
        $_GET[$key] = $value;
        $_POST[$key] = $value;

        return $value;
    }

    public function all(): array
    {
        $data = $_REQUEST;
        unset($data['url'], $data['_token']);

        return $data;
    }
}
