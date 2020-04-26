<?php

namespace Jascha030\WP\Subscriptions\Provider\Auto;

use Jascha030\WP\Subscriptions\Provider\Provider;
use ReflectionClass;
use ReflectionException;
use ReflectionMethod;

class AutoProvider
{
    USE Provider;

    /**
     * @var ReflectionClass
     */
    protected $reflector;

    /**
     * AutoProvider constructor.
     *
     * @throws ReflectionException
     */
    public function __construct()
    {
        $this->reflector = new ReflectionClass($this);
    }

    /**
     * @param array|null $exclude
     *
     * @return array
     */
    protected function init(array $exclude = null)
    {
        $hooks = [];

        foreach ($this->reflector->getMethods(ReflectionMethod::IS_PUBLIC) as $method) {
            $name = $method->getName();

            if (strpos($name, '__') !== 0) {
                continue;
            }

            if (! empty($exclude)) {
                if (in_array($name, $exclude)) {
                    continue;
                }
            }

            $hooks[$this->fromCamelCase($name)] = $name;
        }

        return $hooks;
    }

    /**
     * @param string $input
     *
     * @return string
     */
    protected function fromCamelCase(string $input)
    {
        preg_match_all('!([A-Z][A-Z0-9]*(?=$|[A-Z][a-z0-9])|[A-Za-z][a-z0-9]+)!', $input, $matches);
        $ret = $matches[0];
        foreach ($ret as &$match) {
            $match = $match == strtoupper($match) ? strtolower($match) : lcfirst($match);
        }

        return implode('_', $ret);
    }
}
