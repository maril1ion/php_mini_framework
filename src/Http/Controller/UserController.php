<?php

declare(strict_types=1);

namespace App\Http\Controller;

use App\Http\Response;
use App\Http\Route\Route;
use App\Http\Request;

final class UserController
{
	/**
	 * @param array<string,string> $params
	 */
	#[Route(path: '/user/{id}', methods: ['GET'])]
	public function show(Request $request, array $params): Response
	{
		$id = $params['id'] ?? null;
		return new Response("User ID: $id");
	}
}
