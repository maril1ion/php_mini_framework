<?php
declare(strict_types=1);

namespace App\Http\Controller;

use App\Http\Response;
use App\Http\Route\Route;
use App\Http\Request;
use App\Database\Database;
final class DatabaseController
{
	public function __construct(private Database $database) {}

	/**
	 * @param array<string,string> $params
	 */
	#[Route(path: '/database', methods: ['GET'])]
	public function show(Request $request, array $params): Response
	{
		$tableName = 'users';
		$this->database->query(
			"CREATE TABLE IF NOT EXISTS $tableName (id INTEGER PRIMARY KEY AUTOINCREMENT,name TEXT NOT NULL, email TEXT NOT NULL);",
		);
		$this->database->query(
			"INSERT INTO $tableName (name, email) VALUES (:name, :email);",
			[
				':name' => 'Marillion',
				':email' => 'maril@mail.com',
			],
		);
		$data = $this->database->query("SELECT * FROM $tableName");
		$params = [
			'name' => 'Database',
			'status' => 'ok',
			'message' => $data,
		];
		return Response::json($params);
	}
}
