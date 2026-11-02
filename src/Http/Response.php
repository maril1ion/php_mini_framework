<?php
declare(strict_types=1);
namespace App\Http;

final class Response
{
    private int $status;
    private array $headers = [];
    private string $body = '';
    public function __construct(string $body = '', int $status = 200, array $headers = [])
    {
        $this->body = $body;
        $this->status = $status;
        $this->headers = $headers;
    }
    public function withStatus(int $status)
    {
        return new self($this->body, $status, $this->headers);
    }

    public function withHeader($key, $value)
    {
        return new self($this->body,$this->status, array_merge($this->headers, [$key => $value]), );
    }

    public function withBody(string $body)
    {
        return new self($body, $this->status, $this->headers);
    }



}