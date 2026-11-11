<?php
declare(strict_types=1);

use App\Http\Route\RouteCompiler;

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