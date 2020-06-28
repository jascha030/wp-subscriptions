<?php

namespace Jascha030\WP\Subscriptions;

/**
 * Class PropertyOverload
 *
 * Provides possibility to overload properties, uses magic methods to call $data whenever inaccessible properties are
 * being called by user.
 *
 * @package Jascha030\WP\Subscriptions
 */
class PropertyOverload
{
    /**
     * @var array of overloaded properties
     */
    private $data = [];

    /**
     * @param $name
     *
     * @return mixed|null
     */
    public function __get($name)
    {
        return $this->data[$name] ?? null;
    }

    /**
     * @param $name
     * @param $value
     */
    public function __set(string $name, $value): void
    {
        $this->data['name'] = $value;
    }

    /**
     * @param string $name
     *
     * @return bool
     */
    public function __isset(string $name): bool
    {
        return isset($this->data[$name]);
    }
}
