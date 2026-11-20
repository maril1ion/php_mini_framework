<?php

namespace App\Http\Container;
use http\Exception\RuntimeException;
use Psr\Container\ContainerInterface;
use ReflectionClass;
use Exception;
use ReflectionException;
class Container implements ContainerInterface
{
    private array $factories = [];
    private array $instances = [];
    private array|bool $building = [];
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
            if ($type !== null && !$type->isBuiltin()) {
                // Получаем имя класса (например, "App\Http\Container\Database")
                $typeName = $type->getName();

                // 2. Спрашиваем у контейнера: умеешь ли ты создавать такой тип?
                if ($this->has($typeName)) {
                    $args[] = $this->get($typeName); // Получаем объект из контейнера
                    continue;
                }
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