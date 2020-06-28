<?php

namespace Jascha030\WP\Subscriptions\Shared\Container;

use Closure;
use Jascha030\WP\Subscriptions\Shared\Singleton;
use Jascha030\WP\Subscriptions\Subscription;
use Psr\Container\ContainerInterface;
use RuntimeException;

/**
 * Class Container
 *
 * @package Jascha030\WP\Subscriptions\Shared\Container
 */
class Container extends Singleton implements ContainerInterface
{
    protected $resolved = [];

    protected $bindings = [];

    protected $entries = [];

    /**
     * @param string $id
     * @param array $params
     *
     * @return \Closure|mixed|string
     * @throws \Exception
     */
    final public function get($id, array $params = [])
    {
        return $this->resolve($id);
    }

    /**
     * @param string $id
     *
     * @return bool
     */
    final public function has($id): bool
    {
        return $this->bound($id) || $this->resolved($id);
    }

    /**
     * @param string $abstract
     * @param string|\Closure|mixed|null $concrete Class constant / Closure / Object
     * @param bool $shared
     */
    public function bind(string $abstract, $concrete = null, $shared = false): void
    {
        if (is_null($concrete) && class_exists($abstract)) {
            $concrete = $abstract;
        }

        if (is_object($concrete)) {
            $this->resolved[$abstract] = true;
            $this->entries[$abstract]  = $concrete;
        }

        $this->bindings[$abstract]['concrete'] = $concrete;
        $this->bindings[$abstract]['shared']   = $shared;
    }

    /**
     * @param $abstract
     * @param array $arguments
     *
     * @return mixed
     * @throws \Exception
     */
    public function make($abstract, $arguments = [])
    {
        if (! $this->bound($abstract)) {
            throw new RuntimeException("Abstract {$abstract}, not bound");
        }

        if (! $this->factoryInstance($abstract)) {
            throw new RuntimeException("Concrete for {$abstract} does not implement " . Subscription::class);
        }

        return call_user_func([$this->concrete($abstract), 'create'], $arguments);
    }

    /**
     * @param $abstract
     * @param array $parameters
     *
     * @return \Closure|mixed|string
     * @throws \Exception
     */
    protected function resolve(string $abstract, $parameters = [])
    {
        if ($this->resolved($abstract)) {
            return $this->entries[$abstract];
        }

        if (! $this->bound($abstract)) {
            throw new RuntimeException("Abstract: {$abstract}, not bound");
        }

        $concrete = $this->bindings[$abstract]['concrete'];

        if ($concrete instanceof Closure) {
            $entry = $concrete(...$parameters);
        }

        if (is_string($concrete) && class_exists($concrete)) {
            $entry = empty($parameters) ? new $concrete() : $concrete(...$parameters);
        }

        if (is_object($concrete) && ! $concrete instanceof Closure) {
            $entry = $concrete;
        }

        if (! isset($entry)) {
            throw new RuntimeException("Entry for {$abstract} could not be resolved");
        }

        $this->entries[$abstract]  = $entry;
        $this->resolved[$abstract] = true;

        return $entry;
    }

    protected function bound($abstract): bool
    {
        return isset($this->bindings[$abstract]);
    }

    protected function factoryInstance($abstract): bool
    {
        return (class_exists($abstract) && is_subclass_of(Subscription::class, $abstract));
    }

    protected function concrete($abstract)
    {
        return $this->bindings[$abstract]['concrete'];
    }

    /**
     * Check concrete for binding and return it if it exists.
     *
     * @param string $abstract
     *
     * @return mixed|string
     */
    protected function concreteBinding(string $abstract)
    {
        $binding = $this->bindings[$abstract];

        return is_object($binding['concrete']) ? $binding['concrete'] : $abstract;
    }

    protected function resolved($abstract): bool
    {
        return isset($this->resolved[$abstract]) && $this->resolved[$abstract];
    }
}
