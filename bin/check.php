<?php
# CLI environment diagnostics
declare(strict_types=1);
require __DIR__ . '/../vendor/autoload.php';



//PHP: 8.4.27 (cli)
//Extensions: pdo_mysql, redis, xdebug, intl, zip, bcmath, opcache
//MySQL: 8.4.x
//Redis: PONG
$arrayExtensions = [
    'pdo_mysql','redis','xdebug','intl','zip','bcmath','opcache'
];

$loadedExtensions = array_filter(
    $arrayExtensions,
    static fn (string $extension): bool => extension_loaded($extension)
);
$array = [
    'PHP' => PHP_VERSION .' ('. PHP_SAPI.')',
    'Extensions' => $loadedExtensions,
];

foreach ($array as $key => $value) {
    printf(
        '%s: %s%s',
        $key,
        $value ? 'OK' : 'MISSING',
        PHP_EOL
    );
}