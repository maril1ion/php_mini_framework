<?php
declare(strict_types=1);

use App\Http\Route\RouteCompiler;
use App\Http\Router;
use App\Http\Request;
use App\Http\Response;
use App\Http\Container\Container;
test('it compiles path with placeholder to regex', function () {
    $compiler = new RouteCompiler();
    $regex = $compiler->compiler('/users/{id}');

    expect($regex)->toBe('#^/users/(?P<id>[^/]+)$#');
});

test('it matches path against compiled regex', function () {
    $compiler = new RouteCompiler();
    $regex = $compiler->compiler('/users/{id}');

    // Проверяем, совпадает ли реальный путь с сгенерированной регуляркой
    $path = '/users/123';
    preg_match($regex, $path, $matches);

    expect($matches['id'] ?? null)->toBe('123');
});

test('it passes dynamic parameters to handler', function () {
    $container = new Container();
    $router = new Router($container);
    $router->get('/user/{id}', function (Request $request, array $params) {
        return new Response('User ' . $params['id']);
    });
    $request = new Request('GET','/user/42');
    $response = $router->dispatch($request);

    expect($response->status())->toBe(200)
        ->and($response->body())->toBe('User 42');

});