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
    public function __construct(string $body = '', array $headers = [], int $status = 200)
    {
        $this->body = $body;
        $this->status = $status;
        $this->headers = $headers;
    }
    public function withStatus(int $status): self
    {
        return new self($this->body,$this->headers, $status);
    }

    public function withHeader(string $key, string $value): self
    {
        return new self($this->body,array_merge($this->headers, [$key => $value]), $this->status);
    }

    public function withBody(string $body): self
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
		public static function json(array $data, int $status=200): self
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
