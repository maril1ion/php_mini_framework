<?php
declare(strict_types=1);
use Predis\Client as RedisClient;
require __DIR__ . '/../vendor/autoload.php';
$arrayExtensions = [
    'pdo_mysql','redis','xdebug','intl','zip','bcmath','Zend OPcache'
];

$loadedExtensions = array_filter(
    $arrayExtensions,
    static fn (string $extension): bool => extension_loaded($extension)
);
function missExtensions(array $arrayExtensions): array
{
	$getLoaded = get_loaded_extensions();
	return array_diff($arrayExtensions, $getLoaded);
}
function pdoMysqlVersion()
{
	try {
    	$host = getenv('DB_HOST') ?: 'db';
     $db = getenv('DB_NAME') ?: 'db';
     $user = getenv('DB_USER') ?: 'root';
     $pass = getenv('DB_PASSWORD') ?: '';
     $port = getenv('DB_PORT') ?: '3306';

     $dsn = "mysql:host=$host;dbname=$db;port=$port;charset=utf8mb4";
     $pdo = new PDO($dsn,$user,$pass);
     return [
     'ok' => true,
     'value' => $pdo -> getAttribute(PDO::ATTR_SERVER_VERSION)];
	} catch (PDOException $e) {
		return [
			'ok' => false,
			'value' => "Error: " . $e->getMessage()
			];
	}
}
function redisPingPong()
{
	try {
        $redis = new RedisClient([
            'scheme' => 'tcp',
            'host' => getenv('REDIS_HOST') ?: 'cache',
            'port' => (int) (getenv('REDIS_PORT') ?: 6379),
            'password' => getenv('REDIS_PASSWORD') ?: null,
        ]);
        return [
        'ok' => true,
        'value' => $redis->ping()
        ];
    } catch (Throwable $e) {
    return [
    'ok' => false,
    'value' => 'Error: ' . $e->getMessage()
    ];
    }
}

$mysql = pdoMysqlVersion();
$redis = redisPingPong();

// echo array
$array = [
    'PHP' => PHP_VERSION .' ('. PHP_SAPI.')',
    'Extensions' => implode(', ',$loadedExtensions),
    'Missing' => implode(', ',missExtensions($arrayExtensions)),
    'MySQL' => $mysql['value'],
    'Redis' => $redis['value'],
];

foreach ($array as $key => $value) {
    printf(
        '%s: %s%s',
        $key,
        $value ? $value : ' ',
        PHP_EOL
    );
}
$ok = true;
$ok = $ok && $mysql['ok'];
$ok = $ok && $redis['ok'];

if ($ok) {
	exit(0);
}else {
	exit(1);
}
