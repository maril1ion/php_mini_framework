<?php
declare(strict_types=1);
namespace App\Database;
use PDO;
class Database
{
//string $dsn,
//string $host,
//string $port,
//string $dbname,
//string $username,
//string $password,
//
//DB_HOST=host.docker.internal
//DB_PORT=3306
//DB_DATABASE=db
//DB_NAME=db
//DB_USER=root
//DB_PASSWORD=root
//DB_ROOT_PASSWORD=root
    public function __construct(private PDO $pdo)
    {

    }

    public function query()
    {
        return 'Пока только загрушка ввиде строки';
    }

    public function prepare()
    {

    }

    public function execute()
    {

    }
}