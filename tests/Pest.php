<?php
declare(strict_types=1);
use App\Http\Response;
use App\Http\Router;
use App\Http\Request;
use App\Http\Container\Container;

uses()->beforeEach(function () {
    $container = new Container();
    $router = new Router($container);
    $router->get('/', fn(Request $req) => new Response('Home'));
    $router->post('/submit', fn(Request $req) => new Response('Submitted', [], 201));
})->in('Unit');