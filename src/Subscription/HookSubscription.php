<?php

namespace Jascha030\WPSI\Subscription;

use Jascha030\WPSI\Exception\InvalidMethodException;

/**
 * Class HookSubscription
 *
 * @package Jascha030\WPSI\Subscription
 */
class HookSubscription implements Subscription
{
    private $hook;

    private $class;

    private $arguments = [];

    private $method = '';

    /**
     * HookSubscription constructor.
     *
     * @param string $hook
     * @param $class
     * @param $arguments
     */
    public function __construct(string $hook, $class, $arguments)
    {
        $this->hook = $hook;

        $this->class = $class;

        $this->arguments = $arguments;
    }

    /**
     * @return string
     */
    public function getHook()
    {
        return $this->hook;
    }

    /**
     * @return mixed
     */
    public function getClass()
    {
        return $this->class;
    }

    /**
     * @return mixed
     */
    public function getArguments()
    {
        return $this->arguments;
    }

    /**
     * @return mixed
     */
    public function getMethod()
    {
        return $this->method; // This Subscription is not meant to be used directly.
    }

    public function subscribe()
    {
        $parameters = $this->getArguments();

        if (is_string($parameters)) {
            $arguments = [$this->getHook(), [$this->getClass(), $parameters]];
        } elseif (is_array($parameters) && isset($parameters[0])) {
            $arguments = [
                $this->getHook(),
                [$this->getClass(), $parameters[0]],
                isset($parameters[1]) ? $parameters[1] : 10,
                isset($parameters[2]) ? $parameters[2] : 1
            ];
        }

        if (isset($arguments)) {
            switch ($this->getMethod()) {
                case SubscriptionMethodTypes::ACTION:
                    add_action(...$arguments);
                    break;
                case SubscriptionMethodTypes::FILTER:
                    add_filter(...$arguments);
                    break;
                default:
                    throw new InvalidMethodException();
                    break;
            }
        }
    }
}
