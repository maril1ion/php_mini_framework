<?php

declare(strict_types=1);

namespace App\Http;

use App\Http\Route\RouteCompiler;
use ReflectionClass;
use App\Http\Route\Route;

final class Router
{
	/**
	 *

		* @var array<string, array<string, array{regex: string, handler: mixed}>>

	 */
	private array $routes = [];
	private RouteCompiler $compiler;
	public function __construct()
	{
		$this->compiler = new RouteCompiler();
	}
	/**
	 * @param string $method
	 * @param string $path
	 * @param callable|array{object, string} $handler
	 */
	public function addRoute(string $method, string $path, mixed $handler): void
	{
		$this->routes[strtoupper($method)][$path] = [
			'regex' => $this->compiler->compiler($path),
			'handler' => $handler,
		];
	}
	/**
	 * @param string[] $methods
	 */
	private function sendCode405(array $methods): Response
	{
		return new Response(
			'Method Not Allowed',
			['Allow' => implode(', ', array_unique($methods))],
			405,
		);
	}

	private function sendCode404(): Response
	{
		return new Response('Not Found', [], 404);
	}
	public function dispatch(Request $request): Response
	{
		$path = $request->path();
		$method = $request->method();
		$allowedMethods = [];
		// $routerMethod = GET, POST ....

		foreach ($this->routes as $routerMethod => $routes) {
			// $routePath -/ || /health || /user/id ...
			foreach ($routes as $routePath => $route) {
				if (preg_match($route['regex'], $path, $matches)) {
					if ($routerMethod === $method) {
						$params = array_filter(
							$matches,
							static fn($key) => is_string($key),
							ARRAY_FILTER_USE_KEY,
						);
						return $route['handler']($request, $params);
					}
					// methods found, but method not that
					$allowedMethods[] = $routerMethod;
				}
			}
		}
		if (!empty($allowedMethods)) {
			return $this->sendCode405($allowedMethods);
		}
		return $this->sendCode404();
	}

	/**
	 * @param array<string,string> $controllers
	 */
	public function registerController(array $controllers): void
	{
		/**
  @var class-string $controllerClass
 */
		foreach ($controllers as $controllerClass) {
			$reflection = new ReflectionClass($controllerClass);
			foreach ($reflection->getMethods() as $method) {
				$attributes = $method->getAttributes(Route::class);

				foreach ($attributes as $attribute) {
					$route = $attribute->newInstance();

					foreach ($route->methods as $httpMethod) {
						$this->addRoute($httpMethod, $route->path, [
							new $controllerClass(),
							$method->getName(),
						]);
					}
				}
			}
		}
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
