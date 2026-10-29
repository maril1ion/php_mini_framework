<?php
declare(strict_types=1);

require __DIR__ . "/../vendor/autoload.php";

header("Content-Type: application/json; charset=utf-8");

# 2. Читаем сырые данные, которые прислал curl
$rawJson = file_get_contents('php://input');
// 3. Отдаем этот текст обратно "как есть"
echo $rawJson;
// Отправляем ответ обратно в curl для проверки
header('Content-Type: application/json');
echo json_encode([
    'status' => 'debug',
    'raw_body_length' => strlen($rawBody),
    'raw_body_content' => $rawBody
]);