<?php
declare(strict_types=1);
namespace App\Http;

final class Router {
    /**
        * @var array<string, mixed> $routes
    */
 private array $routes = [];
    public function addRoute(string $method, string $path, mixed $handler): void
    {
        $this->routes[strtoupper($method)][$path] = $handler;
    }
    public function dispatch(Request $request): Response
    {
        $path = $request->path();
        $method = $request->method();
        if (isset($this->routes[$method][$path])) {
            $handler = $this->routes[$method][$path];
            return $handler($request);
        }
        //path for others methods - (errors - 405)
        $pathExistsForOtherMethods = false;
        $allowedMethods = [];

        foreach ($this->routes as $routerMethod => $paths) {
            if (array_key_exists($path, $paths)) {
                $pathExistsForOtherMethods = true;
                $allowedMethods[] = $routerMethod;
            }
        }
        if ($pathExistsForOtherMethods) {
            return new Response(
                'Method Not Allowed',
                ['Allow' => implode(', ', $allowedMethods)],
                405
            );
        }
        return new Response('Not Found', [], 404);
    }
    public function get(string $path, mixed $handler): void
    {
        $this->addRoute('GET', $path, $handler);
    }

    public function post(string $path, mixed $handler): void
    {
        $this->addRoute('POST', $path, $handler);
    }

    public function put(string $path, mixed $handler): void
    {
        $this->addRoute('PUT', $path, $handler);
    }

    public function delete(string $path, mixed $handler): void
    {
        $this->addRoute('DELETE', $path, $handler);
    }

}
