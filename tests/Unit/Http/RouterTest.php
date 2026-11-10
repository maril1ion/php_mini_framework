<?php
declare(strict_types=1);
use App\Http\Router;
use App\Http\Response;
use App\Http\Request;
//200 404 505
beforeEach(function () {
    $this->router = new Router();
    $this->router->get('/', fn(Request $req) => new Response('Home'));
    $this->router->post('/submit', fn(Request $req) => new Response('Submitted', [], 201));
});

    test('Router returns 200 with know route', function () {
    $request = new Request('GET','/');
        $response = $this->router->dispatch($request);
        expect($response->status())->toBe(200)
            ->and($response->body())->toBe('Home');
    });

test('Router returns 404 with unknow route', function () {
    $request = new Request('GET','/unknow');
    $response = $this->router->dispatch($request);
    expect($response->status())->toBe(404)
        ->and($response->body())->toBe('Not Found');
});

test('Router returns 405 with incorrect method', function () {
    $request = new Request('POST','/');
    $response = $this->router->dispatch($request);
    expect($response->status())->toBe(405)
        ->and($response->body())->toBe('Method Not Allowed');
});