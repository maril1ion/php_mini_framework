<?php

namespace App\Http\Container;
use phpDocumentor\Reflection\PseudoTypes\False_;
use Psr\Container\ContainerInterface;
use ReflectionClass;
use Exception;
use ReflectionException;
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
    private function resolveParameter(string $id): mixed
    {
        if (!class_exists($id)) {
            throw new Exception("Class {$id} not found");
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
            //проверка явно ли зарегестрированна
            if (array_key_exists($name, $this->instances)) {
                $args[] = $this->instances[$name];
                continue;
            }
            $type = $parameter->getType();
            // является ли объект встроенным
            if ($type !== null && !$type->isBuiltin()) {
                $args[] = $this->get($type->getName());
                continue;
            }
            if ($parameter->isDefaultValueAvailable()) {
                $args[] = $parameter->getDefaultValue();
            }else {
                throw new Exception('Parameter ' . $name . ' not available');
            }
        }
        return $reflection -> newInstanceArgs($args);
    }
}