<?php

namespace Jascha030\WPSI\Subscription;

class Subscription
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
    public function getMethod(): string
    {
        return $this->method;
    }

    /**
     * @return mixed
     */
    public function getArguments(): array
    {
        return $this->arguments;
    }
}
