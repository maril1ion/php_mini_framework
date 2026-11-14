<?php

declare(strict_types=1);

namespace App\Http\Controller;

use App\Http\Response;
use App\Http\Route\Route;
use App\Http\Request;

final class GetController
{

  #[Route(path: '/', methods: ['GET'])]
  public function show(Request $request, array $params): Response
  {
    $params = [
      'name' => 'PHP mini-framework',
      'status' => 'ok'
    ];
    return Response::json($params);
  }
}
