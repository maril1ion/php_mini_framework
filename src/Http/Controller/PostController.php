<?php

declare(strict_types=1);

namespace App\Http\Controller;

use App\Http\Response;
use App\Http\Route\Route;
use App\Http\Request;

final class PostController
{
	/**
	 * @param array<string,string> $params
	 */
	#[Route(path: '/health', methods: ['POST'])]
	public function show(Request $request, array $params): Response
	{
		$params['status'] = 'UP';
		return Response::json($params);
	}
}
