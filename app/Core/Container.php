<?php

declare(strict_types=1);

namespace App\Core;

use App\Core\Exception\ContainerException;
use Closure;
use DI\Container as DIContainer;
use DI\ContainerBuilder;
use DI\DependencyException;
use DI\NotFoundException;
use Exception;

class Container
{
    private DIContainer $container;

    public function __construct()
    {
        try {
            $builder = new ContainerBuilder();
            $builder->addDefinitions();
            $this->container = $builder->build();
        } catch (Exception) {
            exit("Building container failed.");
        }
    }

    public function inject(string $class, Closure $initializer): void
    {
        $this->container->set($class, $initializer);
    }

    /**
     * @template T
     * @param string|class-string<T> $class
     *
     * @return mixed|T
     *
     * @throws ContainerException
     */
    public function get(string $class)
    {
        try {
            return $this->container->get($class);
        } catch (DependencyException|NotFoundException $e) {
            throw new ContainerException($e->getMessage());
        }
    }
}
