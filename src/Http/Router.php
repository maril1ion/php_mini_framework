<?php
declare(strict_types=1);
namespace App\Http;

final class Router {
	/**
 * @var array<string, mixed> $routes
 */
 private array $routes = [];
    public function addRoute(string $path, callable $handler): void
    {
        $this->routes[$path] = $handler;
    }
    public function dispatch(Request $request): Response
    {
        $path = $request->path();
        $routes = $this->routes[$path] ?? [];
        if (array_key_exists($path, $this->routes)) {
            $handler = $this->routes[$path];
            return $handler($request);
        }
        return new Response('Not Found', [], 404);

    }
}
