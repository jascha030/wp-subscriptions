<?php

namespace Jascha030\WP\Subscriptions\Factory;

/**
 * Class HookFactory
 *
 * @package Jascha030\WP\Subscriptions\Factory
 */
class HookFactory implements Factory
{
    public function create($provider, array $arguments = [])
    {
        $subscriptions = [];

        if (!$arguments['type']) {
            throw new \Exception(static::class . ' expects argument "type"');
        }

        foreach ($provider->getData() as $tag => $parameters) {
            $type = $arguments['type'];

            $method   = ($parameters) ? $parameters[0] : $parameters;
            $callable = [$provider, $method];

            if (is_string($provider)) {
                $reflectionMethod = new \ReflectionMethod($provider, $method);

                // Todo: does this even make sense?
                if (! $reflectionMethod->isStatic()) {
                    $callable = function (...$params) use ($provider, $method) {
                        static $_instance;

                        if (is_null($_instance)) {
                            return call_user_func([$_instance, $method], ...$params);
                        }

                        $_instance = new $provider();

                        return $_instance;
                    };
                }

                $priority          = (is_array($parameters)) ? $parameters[1] ?? 10 : 10;
                $acceptedArguments = (is_array($parameters)) ? $parameters[2] ?? 1 : 1;
                $subscriptions[] = new $type($tag, $callable, $priority, $acceptedArguments);
            }

            return $subscriptions;
        }
    }
}