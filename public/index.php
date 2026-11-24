<?php
declare(strict_types=1);
use App\Http\Request;
use App\Http\Router;
use App\Http\Controller\UserController;
use App\Http\Controller\PostController;
use App\Http\Controller\GetController;
use App\Http\Container\Container;
use App\Database\Database;
use App\Http\Controller\DatabaseController;
require __DIR__ . '/../vendor/autoload.php';
$request = Request::fromGlobals();
$container = new Container();
$container->set(PDO::class, fn() => new PDO('sqlite::memory:'));
$container->set(Database::class, function ($c) {
	return new Database($c->get(PDO::class));
});
$router = new Router($container);
$router->registerController([
	UserController::class,
	PostController::class,
	GetController::class,
	DatabaseController::class,
]);
$response = $router->dispatch($request);
$response->send();
