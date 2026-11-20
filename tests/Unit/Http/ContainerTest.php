<?php
use App\Http\Container\Container;
use App\Http\Router;

class ForTest{}
class CircularA { public function __construct(public CircularB $b) {} }
class CircularB { public function __construct(public CircularA $a) {} }

test('Container PSR-11 test', function() {
    $container = new Container();
    $container -> set(ForTest::class, function () {
        return new ForTest();
    });
    expect($container -> has(ForTest::class)) -> toBeTrue();
    $service = $container->get(ForTest::class);
    expect($service)->toBeInstanceOf(ForTest::class);
});

test('Container PSR-11 Singleton test', function() {
    $container = new Container();

    $container -> set(ForTest::class, fn () => new ForTest());
    $service1 = $container->get(ForTest::class);
    $service2 = $container->get(ForTest::class);
    expect($service1)->toBe($service2);
});


test('Test class for reflection', function () {
    $container = new Container();
    expect($container -> get(ForTest::class)) -> toBeInstanceOf(ForTest::class);
});


test('Test class for reflection singleton', function () {
    $container = new Container();
    $service1 = $container->get(ForTest::class);
    $service2 = $container->get(ForTest::class);
    expect($service1)->toBe($service2);
});

test('it throws exception on circular dependency', function () {
    $container = new Container();

    expect(fn() => $container->get(CircularA::class))
        ->toThrow(\Exception::class);
});

