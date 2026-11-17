<?php
use App\Http\Container\Container;
class ForTest{}
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