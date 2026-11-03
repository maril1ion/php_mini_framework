<?php
declare(strict_types=1);
namespace App\Http;

final class Response
{
    private readonly int $status;
    /**
     * @var array<string,string>
     */
    private readonly array $headers;
    private readonly string $body;
    /**
     * @param array<string,string> $headers
     */
    public function __construct(string $body = '', int $status = 200, array $headers = [])
    {
        $this->body = $body;
        $this->status = $status;
        $this->headers = $headers;
    }
    public function withStatus(int $status): self
    {
        return new self($this->body, $status, $this->headers);
    }

    public function withHeader(string $key, string $value): self
    {
        return new self($this->body, $this->status, array_merge($this->headers, [$key => $value]));
    }

    public function withBody(string $body): self
    {
        return new self($body, $this->status, $this->headers);
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
//json(), html(), redirect()
		public static function send(string $body, string $type = 'text/html; charset=utf-8', int $status = 200): void
		{
		    http_response_code($status);
		    header('Content-Type: ' . $type);
		    echo $body;
				exit;
		}
		public static function json(mixed $data, int $status=200) {
          self::send(
              json_encode($data, JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR),
              'application/json; charset=utf-8',
              $status
          );
    }

    public static function html(string $value): string
    {
        return htmlspecialchars($value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
    }

    public static function redirect(string $url, int $status = 302): never
    {
        header('Location: ' . $url, true, $status);
        exit;
    }


}
