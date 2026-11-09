<?php
declare(strict_types=1);
namespace App\Http;

final class Request
{
    /**
     * @param array<string,mixed> $headers
     * @param array<string,mixed> $cookies
     * @param array<string,mixed> $post
     * @param array<string,mixed> $query
     */
    public function __construct(
        private readonly string $method,
        private readonly string $path,
        private readonly array $query,
        private readonly array $post,
        private readonly array $headers,
        private readonly array $cookies,
    )
    {}
    public static function fromGlobals(): self
    {
        // Ошибка в журнал - использовал прямые вызовы методов. Испаравил через методы.
        $method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
        $raw = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH);
        $path = is_string($raw) && $raw !== '' ? $raw : '/';;
        $query = $_GET;
        $post = $_POST;
        $cookies = $_COOKIE;

        $headers = [];
        foreach ($_SERVER as $key => $value) {
            if (str_starts_with($key, 'HTTP_')) {
                $headers[$key] = $value;
            }
        }
        return new self($method, $path, $query, $post, $headers, $cookies);
        }
    public function method(): string { return $this->method; }
    public function path(): string { return $this->path; }
    public function query(string $key, ?string $default = null): ?string { return $this->query[$key] ?? $default; }
    public function post(string $key, ?string $default = null): ?string
    {
    	return $this -> post[$key] ?? $default;
    }
    public function header(string $name, ?string $default = null): ?string
    {
    $key = 'HTTP_' . strtoupper(str_replace('-', '_', $name));
    return $this->headers[$key] ?? $default;
    }

    public function cookie(string $key, ?string $default = null): ?string
    {
    	return $this -> cookies[$key] ?? $default;
    }
}
