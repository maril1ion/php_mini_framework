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
				'x-initial' => 'a',
				'content-type' => 'text/plain'
			]);
			expect($response->headers())->toBe(['x-initial' => 'a']);
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
			expect($response->headers()['content-type'])->toBe('application/json; charset=utf-8');
			expect($response->body())->toContain('кот');
			expect($response->body())->not()->toContain('\u034');
		});
//Создание Response со статусом ровно 100 и ровно 599 (проверка границ).
// Создание Response с корректными заголовками (например, Content-Type, X-Custom_Header).
// Проверка, что withHeader перезаписывает существующий заголовок, если передан в другом регистре (например, был content-type, передали Content-Type — в массиве должен остаться один ключ content-type с новым значением).
// Метод json() возвращает Response со статусом 200, заголовком application/json; charset=utf-8 и правильным телом.
// Метод json() корректно кодирует кириллицу (проверка JSON_UNESCAPED_UNICODE).
        test('Response can be created with status 100 (lower boundary)', function ()
        {
            $response = new Response(status:100);
            expect($response->status()) -> toBe(100);
        });

        test('Response can be created with status 599 (high boundary)', function ()
        {
            $response = new Response(status:599);
            expect($response->status()) -> toBe(599);
        });

        test('Response can be created with status 99 (below lower boundary)', function ()
        {
            expect(fn() => new Response(status:99)) -> toThrow(InvalidArgumentException::class);
        });

        test('Response can be created with status 600 (above high boundary)', function ()
        {
            expect(fn () => new Response(status:600)) -> toThrow(InvalidArgumentException::class);
        });

        test('Invalid header name', function (string $invalidHeaders) {
            expect(fn () =>  new Response(headers: [$invalidHeaders => 'value']))
                ->toThrow(InvalidArgumentException::class);}) ->
                with([
            'space' => ['X Foo'],
            'cyrillic' => ['Кириллица'],
            'empty' => [''],
            'colon' => 'X-Foo:',
        ]);

        test('Response throws on header value containing forbidden characters', function (string $maliciousValue) {
            expect(fn() => new Response(headers: ['X-Test-Header' => $maliciousValue]))
                ->toThrow(InvalidArgumentException::class);
        })->with([
            'CRLF injection' => ["evil\r\nSet-Cookie: hacked"],
            'Carriage Return' => ["evil\rSet-Cookie: hacked"],
            'Line Feed'       => ["evil\nSet-Cookie: hacked"],
            'Null byte'       => ["evil\0Set-Cookie: hacked"],
        ]);

        test('Response collision', function () {
            expect(fn () => new Response(headers: [
                'Content-Type' => 'a',
                'content-type' => 'b'
            ])) -> toThrow(InvalidArgumentException::class);
        });

        test('Response::json throws on invalid data', function () {
            expect(fn() => Response::json([NAN]))->toThrow(JsonException::class);
        });