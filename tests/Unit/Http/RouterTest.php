<?php
declare(strict_types=1);

use App\Http\Router;
use App\Http\Response;
use App\Http\Request;

test('Router returns 200 with known route', function () {
    $router = new Router();
    $router->get('/', fn(Request $req) => new Response('Home'));

    $request = new Request('GET', '/');
    $response = $router->dispatch($request);

    expect($response->status())->toBe(200)
        ->and($response->body())->toBe('Home');
});

test('Router returns 404 with unknown route', function () {
    $router = new Router();
    $router->get('/', fn(Request $req) => new Response('Home'));

    $request = new Request('GET', '/unknown');
    $response = $router->dispatch($request);

    expect($response->status())->toBe(404)
        ->and($response->body())->toBe('Not Found');
});

test('Router returns 405 with incorrect method', function () {
    $router = new Router();
    $router->get('/', fn(Request $req) => new Response('Home'));

    $request = new Request('POST', '/');
    $response = $router->dispatch($request);

    expect($response->status())->toBe(405)
        ->and($response->body())->toBe('Method Not Allowed');
});
