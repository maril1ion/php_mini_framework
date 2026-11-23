<?php
declare(strict_types=1);

namespace App\Http\Controller;

use App\Http\Response;
use App\Http\Route\Route;
use App\Http\Request;
use PDO;
use App\Http\Container\Container;
use App\Database\Database;
final class DatabaseContoller
{
    public function __construct(private Database $database) {}

    /**
     * @param array<string,string> $params
     */
    #[Route(path: '/database', methods: ['GET'])]
    public function show(Request $request, array $params): Response
    {
$tableName = "users";
        $data = $this ->database->prepare($tableName);
        $params = [
            'name' => 'Database',
            'status' => 'ok',
            'message' => $data
        ];
        return Response::json($params);
    }
}
