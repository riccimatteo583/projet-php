<?php

declare(strict_types=1);

namespace App\Core;

use Closure;
use ReflectionClass;
use RuntimeException;

/**
 * A minimal dependency injection container.
 *
 * Supports explicit bindings via closures as well as automatic
 * resolution ("autowiring") of concrete classes through reflection.
 * Bindings are treated as singletons once resolved.
 */
final class Container
{
    /** @var array<string, Closure> */
    private array $bindings = [];

    /** @var array<string, object> */
    private array $instances = [];

    /**
     * Register a factory closure for the given abstract identifier.
     */
    public function bind(string $abstract, Closure $factory): void
    {
        $this->bindings[$abstract] = $factory;
    }

    /**
     * Resolve an entry from the container.
     *
     * @template T of object
     * @param class-string<T> $abstract
     * @return T
     */
    public function make(string $abstract): object
    {
        if (isset($this->instances[$abstract])) {
            return $this->instances[$abstract];
        }

        if (isset($this->bindings[$abstract])) {
            return $this->instances[$abstract] = ($this->bindings[$abstract])($this);
        }

        return $this->instances[$abstract] = $this->autowire($abstract);
    }

    /**
     * Instantiate a concrete class, recursively resolving its
     * constructor dependencies from the container.
     */
    private function autowire(string $class): object
    {
        if (!class_exists($class)) {
            throw new RuntimeException("Cannot resolve unknown class [{$class}].");
        }

        $reflector  = new ReflectionClass($class);
        $constructor = $reflector->getConstructor();

        if ($constructor === null) {
            return new $class();
        }

        $dependencies = [];

        foreach ($constructor->getParameters() as $parameter) {
            $type = $parameter->getType();

            if ($type === null || $type->isBuiltin()) {
                if ($parameter->isDefaultValueAvailable()) {
                    $dependencies[] = $parameter->getDefaultValue();
                    continue;
                }

                throw new RuntimeException(
                    "Cannot resolve scalar dependency \${$parameter->getName()} for [{$class}]."
                );
            }

            $dependencies[] = $this->make($type->getName());
        }

        return $reflector->newInstanceArgs($dependencies);
    }
}
