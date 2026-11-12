<?php
declare(strict_types=1);
namespace App\Http;
use App\Http\Route\RouteCompiler;

final class Router {
    /**
        * @var array<string, callable> $routes
    */
 private array $routes = [];
 private RouteCompiler $compiler;
 public function __construct() {
     $this -> compiler = new RouteCompiler();
 }
    public function addRoute(string $method, string $path, mixed $handler): void
    {
        $this->routes[strtoupper($method)][$path] = [
            'regex' => $this-> compiler-> compiler($path),
            'handler' => $handler
        ];
    }
    public function dispatch(Request $request): Response
    {
        $path = $request->path();
        $method = $request->method();
        var_dump("Method: " . $method . ", Path: " . $path);
        var_dump("Registered routes: " . implode(', ', array_keys($this->routes[$method] ?? [])));
        if (isset($this->routes[$method])) {
            foreach ($this->routes[$method] as $pattern => $route) {
                var_dump("Testing against regex: " . $route['regex']);
                if (preg_match($route['regex'], $path, $matches)) {
                    $params = array_filter(
                        $matches,
                        static fn($key) => is_string($key),
                        ARRAY_FILTER_USE_KEY);
                    return ($route['handler'])($request, $params);
                }
            }
        }
        //path for others methods - (errors - 405)

        $allowedMethods = [];
        $pathExistsForOtherMethods = false;
        foreach ($this->routes as $routerMethod => $routes) {
            foreach ($routes as $pattern => $route) {
                if (preg_match($route['regex'], $path)) {
                    $pathExistsForOtherMethods = true;
                    $allowedMethods[] = $routerMethod;
                }
            }
        }
            if ($pathExistsForOtherMethods) {
                return new Response(
                    'Method Not Allowed',
                    ['Allow' => implode(', ',  array_unique($allowedMethods))],
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
