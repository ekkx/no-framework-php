<?php

declare(strict_types=1);

namespace App\Core;

use App\Core\Exception\ContainerException;
use Closure;
use DI\Container as DIContainer;
use DI\ContainerBuilder;
use Throwable;

class Container
{
    private DIContainer $container;

    public function __construct()
    {
        try {
            $builder = new ContainerBuilder();
            $builder->addDefinitions();
            $this->container = $builder->build();
        } catch (Throwable) {
            exit("Building container failed.");
        }
    }

    /**
     * Injects dependencies into the container.
     *
     * @param array<string|class-string, Closure> $dependencies
     */
    public function inject(array $dependencies): void
    {
        foreach ($dependencies as $key => $value) {
            error_log(json_encode($key));
            $this->container->set($key, $value);
        }
    }

    /**
     * Retrieves an instance of the given class from the container.
     *
     * @template T
     * @param string|class-string<T> $class
     *
     * @return mixed|T
     *
     * @throws ContainerException
     */
    public function make(string $class)
    {
        try {
            return $this->container->get($class);
        } catch (Throwable $e) {
            throw new ContainerException($e->getMessage());
        }
    }
}
