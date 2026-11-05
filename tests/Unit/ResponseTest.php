<?php
declare(strict_types=1);
use App\Http\Response;
		test('Response default to status 200 with empty body and headers', function ()
		{
			$response = new Response();
			expect($response -> status()) -> toBe(200);
			expect($response -> body()) -> toBe("");
			expect($response -> headers()) -> toBe([]);
		});

		test('withStatus returns a new instance and keeps the original unchanged', function ()
		{
			$response = new Response();
			$newObject = $response->withStatus(404);
			expect($newObject)->not()->toBe($response);
			expect($newObject->status())->toBe(404);
			expect($response->status())->toBe(200);
		});

		test('withHeaders returns a new instance and keeps the original unchanged', function ()
		{
			$response = new Response('',[
				'X-Initial' => 'a'
			]);
			$newObject = $response->withHeader('Content-Type', 'text/plain');
			expect($newObject) ->not()->toBe($response);
			expect($newObject->headers())->toBe([
				'X-Initial' => 'a',
				'Content-Type' => 'text/plain'
			]);
			expect($response->headers())->toBe(['X-Initial' => 'a']);
		});

		test('withBody returns a new instance and keeps the original unchanged', function ()
		{
			$response = new Response('');
			$newObject = $response->withBody('new Body');
			expect($newObject)->not()->toBe($response);
			expect($newObject->body())->toBe('new Body');
			expect($response->body())->toBe('');
		});
		test('json sets Content-Type and encodes Cyrillic without escaping', function ()
		{
			$response = new Response()::json(['имя'=>'кот']);
			expect($response->status())->toBe(200);
			expect($response->headers()['Content-Type'])->toBe('application/json; charset=utf-8');
			expect($response->body())->toContain('кот');
			expect($response->body())->not()->toContain('\u034');
		});
