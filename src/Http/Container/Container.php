<?php

namespace App\Http\Container;
use phpDocumentor\Reflection\PseudoTypes\False_;
use Psr\Container\ContainerInterface;
use ReflectionClass;
use ReflectionParameter;
class Container implements ContainerInterface
{
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
    private function resolveParameter(string $id)
    {
        $reflection = new ReflectionClass($id);
        $constructor = $reflection->getConstructor();
        if ($constructor !== null) {
            return $constructor;
        }else {
            return new $id();
        }
        $params = $constructor->getParameters();
        foreach ($params as $parameter) {
            $type = $parameter->getType();
            $this -> get($parameter->getName());
        }

    }
}