<?php
declare(strict_types=1);
namespace App\Http;
use InvalidArgumentException;
use Stringable;
final class Response
{
    private readonly int $status;
    /**
     * @var array<string,string>
     */
    private readonly array $headers;
    private readonly string $body;
    private const int MIN_STATUS = 100;
    private const int MAX_STATUS = 599;
    /**
     * @param array<string,string> $headers
     */

    public function __construct(string|Stringable $body = '', array $headers = [], int $status = 200)
    {
    	self::assertValidStatus($status);
     		$this->body = (string) $body;
        $this->status = $status;
        $this->headers = self::normalizeHeaders($headers);
    }

    private static function assertValidStatus(int $status): void
    {
     if ($status < self::MIN_STATUS || $status > self::MAX_STATUS) {
     throw new InvalidArgumentException(self::class . ": invalid HTTP status $status, expected " . self::MIN_STATUS . "-" . self::MAX_STATUS);
     }
    }
    /**
     * @param array<string,string> $headers
     * @return array<string,string>
     */
    private static function normalizeHeaders(array $headers): array
    {
    	$normalized = [];
     foreach($headers as $name => $value)
     {
         $name =(string) $name;
     if (preg_match('/^[!#$%&\'*+\-.^_`|~0-9A-Za-z]+$/', $name)!== 1) {
         throw new InvalidArgumentException(self::class .' invalid header name: ' . json_encode($name));
     }
     if (preg_match('/[\r\n\x00]/', $value)=== 1) {
          throw new InvalidArgumentException(self::class .' invalid header value: ' . json_encode($name));
      }
     $normalizedKey = strtolower($name);
     if (array_key_exists($normalizedKey, $normalized))
     {
      throw new InvalidArgumentException(self::class .' duplicate header: ' . json_encode($name));
     }
     	$normalized[$normalizedKey] = $value;
     }
     //return:
     //ключи — приведены к нижнему регистру (strtolower);
     //значения — ровно те же, что пришли на вход, без изменений;
     //порядок — сохранён порядок первого появления (PHP-массивы упорядочены).
     return $normalized;
    }

    public function withStatus(int $status): self
    {
        return new self($this->body,$this->headers, $status);
    }

    public function withHeader(string $key, string $value): self
    {
        return new self($this->body,array_merge($this->headers, [strtolower($key) => $value]), $this->status);
    }

    public function withBody(string|Stringable $body): self
    {
        return new self($body,$this->headers, $this->status);
    }

    public function body(): string
    {
        return $this->body;
    }
    public function status(): int
    {
        return $this->status;
    }
    /**
    	* @return array<string,string>
     */
    public function headers(): array
    {
        return $this->headers;
    }
    /**
     * @param array<array-key,mixed> $data
     */
		public static function json(mixed $data, int $status=200): self
		{
      return new self(json_encode($data,JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR),
      ['Content-Type' => 'application/json; charset=utf-8'],
      $status);
    }

    public static function html(string $html, int $status=200): self
    {
     return new self($html, ['Content-Type' => 'text/html; charset=utf-8'], $status);
    }

    public static function redirect(string $url, int $status = 302): self
    {
    return new self('', ['Location' => $url], $status);
}

    public function send (): void
    {
     http_response_code($this->status);
     foreach($this->headers as $name=>$value)
     {
      header("$name: $value");
     }
     echo $this->body;
    }
}
