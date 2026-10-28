<?php
declare(strict_types=1);

require __DIR__ . "/../vendor/autoload.php";

$rawBody = file_get_contents('php://input');

// Декодируем в массив
$jsonData = json_decode($rawBody, true);

// Отправляем ответ обратно в curl для проверки
header('Content-Type: application/json');
echo json_encode([
    'status' => 'received',
    'your_data' => $jsonData
]);