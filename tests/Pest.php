<?php
declare(strict_types=1);
use App\Http\Response;
use App\Http\Router;
use App\Http\Request;
uses()->beforeEach(function () {
    $router = new Router();
    $router->get('/', fn(Request $req) => new Response('Home'));
    $router->post('/submit', fn(Request $req) => new Response('Submitted', [], 201));
})->in('Unit');