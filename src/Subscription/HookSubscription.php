<?php

namespace Jascha030\WPSI\Subscription;

class HookSubscription implements Subscription
{
    private $hook;

    private $class;

    private $method;

    private $arguments = [];

    public function __construct(string $hook, $class = null, $arguments = null, string $method = null)
    {
        $this->hook = $hook;

        if ($class) {
            $this->class = $class;
        }

        if ($arguments) {
            $this->arguments = $arguments;
        }

        if ($method) {
            $this->method = $method;
        }
    }

    /**
     * @return string
     */
    public function getHook()
    {
        return $this->hook;
    }

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
        return $this->method;
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

        switch ($this->getMethod()) {
            case SubscriptionMethodTypes::ACTION:
                add_action(...$arguments);
                break;
            case SubscriptionMethodTypes::FILTER:
                add_filter(...$arguments);
                break;
            default:
                throw new \Exception("Invalid Subscription method");
                break;
        }
    }
}
