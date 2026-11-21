<?php

namespace App\Http\Container;
use Psr\Container\ContainerInterface;
use ReflectionClass;
class Container implements ContainerInterface
{
    /**
     * @var array<string,callable> $factories
     */
    private array $factories = [];

    /**
     * @var array<string,string> $instances
     */
    private array $instances = [];
    /**
     * @var array<string,bool|string> $building
     */
    private array $building = [];

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
        if (isset($this->building[$id])) {
            throw new \Exception("Circular reference for $id");
        }
        if (!class_exists($id)) {
            throw new \Exception(
                'Services not found ' . $id
            );
        }
        $this->building[$id] = true;
        try{
            $obj = $this->resolveParameter($id);
            $this->instances[$id] = $obj;
            return $obj;
        }finally{
            unset($this->building[$id]);
        }
    }
    public function has(string $id): bool
    {
        return isset($this->factories[$id]) || isset($this->instances[$id]);
    }
    private function resolveParameter(string $id): mixed
    {
        if (!class_exists($id)) {
            throw new \RuntimeException("Class {$id} not found");
        }
        $reflection = new ReflectionClass($id);
        $constructor = $reflection -> getConstructor();
        if ($constructor === null) {
          return new $id();
        }
        $params = $constructor->getParameters();
        $args = [];
        foreach ($params as $parameter) {
            $name = $parameter->getName();
            $type = $parameter->getType();
            //проверка явно ли зарегестрированна
            if ($type instanceof \ReflectionNamedType) {
                if (!$type->isBuiltin()) {
                    $typeName = $type->getName();

                    if ($this->has($typeName)) {
                        $args[] = $this->get($typeName); // Получаем объект из контейнера
                        continue;
                    }                }
            }
            if ($parameter->isDefaultValueAvailable()) {
                $args[] = $parameter->getDefaultValue();
            }else {
                throw new \RuntimeException('Parameter ' . $name . ' not available');
            }
        }
        return $reflection -> newInstanceArgs($args);
    }
}