<?php

declare(strict_types=1);
use App\Http\Request;
use App\Http\Router;
use App\Http\Response;
require __DIR__ . '/../vendor/autoload.php';
$request = Request::fromGlobals();
$router = new Router();
$router -> addRoute('/health', function (Request $request)
{
    return Response::json(['status' => 'UP']);
});
$router -> addRoute('/', function (Request $request){
    return Response::json(['name' => 'PHP mini-framework', 'status' => 'ok']);
});
$response = $router -> dispatch($request);
$response->send();
