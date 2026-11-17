<?php

namespace App\Http\Container;
use Psr\Container\ContainerInterface;
class Container implements ContainerInterface
{
    // 2 приватных метода
    // factories and instances
    private array $factories = [];
    private array $instances = [];
    public function set(string $id, callable $factory): void{
        $this->factories[$id] = $factory;
    }

    public function get(string $id)
    {
        if (isset($this->instances[$id])) {
            return $this->instances[$id];
        }
        if (isset($this->factories[$id])) {
            $this->instances[$id] = $this->factories[$id]($this);
            return $this->instances[$id];
        }
    throw new \Exception(
        'Services not found' . $id
    );
    }
    public function has(string $id): bool
    {
        return isset($this->factories[$id]) || isset($this->instances[$id]);
    }
}