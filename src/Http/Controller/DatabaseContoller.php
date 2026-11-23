<?php
declare(strict_types=1);

namespace App\Http\Controller;

use App\Http\Response;
use App\Http\Route\Route;
use App\Http\Request;
use PDO;
use App\Http\Container\Container;

final class DatabaseContoller
{
    public function __construct(private Database $database) {}

    /**
     * @param array<string,string> $params
     */
    #[Route(path: '/database', methods: ['GET'])]
    public function show(Request $request, array $params): Response
    {
        $data = $this->database->query();

        $params = [
            'name' => 'Database',
            'status' => 'ok',
            'database' => $data
        ];
        return Response::json($params);
    }
}
