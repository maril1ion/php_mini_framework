<?php

declare(strict_types=1);

require __DIR__ . '/../vendor/autoload.php';

use Predis\Client as RedisClient;

function jsonResponse(array $data, int $status = 200): never
{
    http_response_code($status);
    header('Content-Type: application/json; charset=utf-8');

    echo json_encode(
        $data,
        JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT
    );

    exit;
}

$requestMethod = $_SERVER['REQUEST_METHOD'] ?? 'GET';
$requestUri = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/';

if ($requestUri === '/health' && $requestMethod === 'GET') {
    $checks = [
        'redis' => false,
        'database' => false,
    ];

    // Redis
    try {
        $redis = new RedisClient([
            'scheme' => 'tcp',
            'host' => getenv('REDIS_HOST') ?: 'cache',
            'port' => (int) (getenv('REDIS_PORT') ?: 6379),
            'password' => getenv('REDIS_PASSWORD') ?: null,
        ]);

        $redis->ping();

        $checks['redis'] = true;
    } catch (Throwable) {
        $checks['redis'] = false;
    }

    // MySQL
    try {
        $host = getenv('DB_HOST') ?: 'db';
        $db = getenv('DB_NAME') ?: 'db';
        $user = getenv('DB_USER') ?: 'root';
        $pass = getenv('DB_PASSWORD') ?: '';
        $port = getenv('DB_PORT') ?: '3306';

        $dsn = "mysql:host=$host;dbname=$db;port=$port;charset=utf8mb4";

        new PDO($dsn, $user, $pass, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_TIMEOUT => 2,
        ]);

        $checks['database'] = true;
    } catch (PDOException) {
        $checks['database'] = false;
    }

    $healthy = $checks['redis'] && $checks['database'];

    jsonResponse(
        [
            'status' => $healthy ? 'UP' : 'DOWN',
            'checks' => $checks,
            'timestamp' => time(),
        ],
        $healthy ? 200 : 503
    );
}

if ($requestUri === '/' && $requestMethod === 'GET') {
    jsonResponse([
        'name' => 'PHP mini-framework',
        'status' => 'ok',
    ]);
}

jsonResponse(
    ['error' => 'Endpoint not found'],
    404
);