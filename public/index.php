<?php

declare(strict_types=1);

use App\Http\Request;
use App\Http\Router;
use App\Http\Controller\UserController;
use App\Http\Controller\PostController;
use App\Http\Controller\GetController;
use App\Http\Container\Container;

require __DIR__ . '/../vendor/autoload.php';
$request = Request::fromGlobals();
$container = new Container();
$router = new Router($container);
$router->registerController(
    [
        UserController::class,
        PostController::class,
        GetController::class
    ]
);
$response = $router->dispatch($request);
$response->send();
