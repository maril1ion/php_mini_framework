<?php
declare(strict_types=1);
namespace App\Database;
use PDO;
use PDOException;
final class Database
{
	private PDO $pdo;
	public function __construct(PDO $pdo) {
		$this-> pdo = $pdo;
	}

	public function query(string $sql, array $params = []): array
	{
		$pdo = $this->pdo;
		$stmt = $pdo->prepare($sql);
		$stmt->execute($params);
		return $stmt->fetchAll();
	}
	public function execute(string $sql, array $params = []): int
	{
		$pdo = $this->pdo;
		$sth = $pdo->prepare($sql);
		$sth->execute($params);
		return $sth->rowCount();
	}
}
