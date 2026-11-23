<?php
declare(strict_types=1);
namespace App\Database;
use PDO;
use PDOException;
class Database
{
    public function __construct()
    {
        try {
            $dsn = "sqlite::memory:";
            $options = [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false
            ];
            return new PDO($dsn);
        } catch (PDOException $e) {
            die('Ошибка подключения к базе данных: ' . $e->getMessage());
        }
    }

    public function query(string $tableName)
    {
        return "CREATE TABLE IF NOT EXISTS $tableName (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    name TEXT NOT NULL,
    email TEXT NOT NULL);";
    }

    public function prepare(string $tableName)
    {
$pdo = new PDO("sqlite::memory:");
        $queryCreate = $this ->query('users');
        $sth_create = $pdo->prepare($queryCreate);
        $sth = $pdo->prepare("SELECT * FROM $tableName");
        $data = $sth -> fetchAll();
        return $data;
    }

    public function execute()
    {

    }
}