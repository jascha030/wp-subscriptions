<?php

namespace Jascha030\WPSI\Subscription;

class HookSubscription
{
    private $hook;

    private $class;

    private $method;

    private $arguments = [];

    public function __construct(string $hook, string $class = null, array $arguments = null, string $method = null)
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
    public function getHook(): string
    {
        return $this->hook;
    }

    /**
     * @return mixed
     */
    public function getArguments(): array
    {
        return $this->arguments;
    }

    /**
     * @return mixed
     */
    public function getMethod(): string
    {
        return $this->method;
    }

    public function subscribe()
    {
        $parameters = $this->getArguments();

        if (is_string($parameters)) {
            call_user_func($this->method, $this->hook, [$this->class, $parameters]);
        } elseif (is_array($parameters) && isset($parameters[0])) {
            call_user_func($this->method, $this->hook, [$this->class, $parameters[0]],
                isset($parameters[1]) ? $parameters[1] : 10, isset($parameters[2]) ? $parameters[2] : 1);
        }
    }
}
