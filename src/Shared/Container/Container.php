<?php

namespace Jascha030\WP\Subscriptions\Shared\Container;

use Jascha030\WP\Subscriptions\Factory\Factory;
use Jascha030\WP\Subscriptions\Shared\Singleton;
use Psr\Container\ContainerInterface;

/**
 * Class Container
 *
 * @package Jascha030\WP\Subscriptions\Shared\Container
 */
class Container extends Singleton implements ContainerInterface
{
    protected static $instance;

    protected $resolved = [];

    protected $bindings = [];

    protected $entries = [];

    protected $shared = [];

    /**
     * @param string $abstract
     * @param string|\Closure|mixed|null $concrete Class constant / Closure / Object
     * @param bool $shared
     */
    public function bind(string $abstract, $concrete = null, $shared = false)
    {
        if (is_null($concrete) && class_exists($abstract)) {
            $concrete = $abstract;
        }
        $this->bindings[$abstract]['concrete'] = $concrete;
        $this->bindings[$abstract]['shared']   = $shared;
    }

    /**
     * @param string $abstract
     * @param Factory $factory
     */
    public function bindFactory(string $abstract, Factory $factory)
    {
        $this->bind($abstract, $factory);
    }

    /**
     * @param string $id
     * @param array $params
     *
     * @return \Closure|mixed|string
     * @throws \Exception
     */
    public function get($id, $params = [])
    {
        return $this->resolve($id);
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
            throw new \Exception("Abstract {$abstract}, not bound");
        }

        if (! $this->factoryInstance($abstract)) {
            throw new \Exception("Concrete for {$abstract} does not implement " . Factory::class);
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
        if (! $this->bound($abstract)) {
            throw new \Exception("Abstract: {$abstract}, not bound");
        }

        if ($this->resolved($abstract)) {
            return $this->shared($abstract) ? $this->shared[$abstract] : $this->entries[$abstract];
        }

        $concrete = $this->bindings[$abstract]['concrete'];
        $prop     = $this->shared($abstract) ? 'entries' : 'shared';

        if ($this->factoryInstance($abstract)) {
            $entry = $this->make($abstract, $parameters);
        }

        if ($concrete instanceof \Closure) {
            $entry = call_user_func($concrete, ...$parameters);
        }

        if (is_string($concrete) && class_exists($concrete)) {
            $entry = empty($parameters) ? new $concrete() : $concrete(...$parameters);
        }

        if (is_object($concrete) && ! $concrete instanceof \Closure) {
            $entry = $concrete;
        }

        if (! isset($entry)) {
            throw new \Exception("Entry for {$abstract} could not be resolved");
        }

        $this->{$prop}[$abstract]  = $entry;
        $this->resolved[$abstract] = true;

        return $entry;
    }

    /**
     * @param string $id
     *
     * @return bool
     */
    public function has($id): bool
    {
        return $this->exists($id);
    }

    /**
     * @param $abstract
     *
     * @return bool
     */
    protected function exists($abstract): bool
    {
        return $this->bound($abstract) || $this->resolved($abstract);
    }

    protected function shared($abstract): bool
    {
        return ! empty($this->shared[$abstract]) || $this->sharedBinding($abstract);
    }

    protected function sharedBinding($abstract): bool
    {
        return $this->bindings[$abstract]['shared'];
    }

    protected function bound($abstract): bool
    {
        return isset($this->bindings[$abstract]);
    }

    protected function factoryInstance($abstract): bool
    {
        return (class_exists(get_class($this->concrete($abstract))) && in_array(Factory::class,
                class_implements(get_class($this->concrete($abstract)))));
    }

    protected function concrete($abstract)
    {
        return $this->bindings[$abstract]['concrete'];
    }

    protected function resolved($abstract)
    {
        return isset($this->resolved[$abstract]) && $this->resolved[$abstract];
    }
}