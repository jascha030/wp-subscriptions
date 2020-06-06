<?php

namespace Jascha030\WP\Subscriptions\Shared\Container;

use Jascha030\WP\Subscriptions\Factory\SubscriptionFactory;
use Jascha030\WP\Subscriptions\Shared\Singleton;
use Psr\Container\ContainerInterface;

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
    public function get($id, $params = [])
    {
        return $this->resolve($id);
    }

    /**
     * @param string $id
     *
     * @return bool
     */
    public function has($id): bool
    {
        return $this->bound($id) || $this->resolved($id);
    }

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
        if ($this->resolved($abstract)) {
            return $this->entries[$abstract];
        }

        if (! $this->bound($abstract)) {
            throw new \Exception("Abstract: {$abstract}, not bound");
        }

        $concrete = $this->bindings[$abstract]['concrete'];

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
        return (class_exists($abstract) && in_array(SubscriptionFactory::class, class_implements($abstract)));
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