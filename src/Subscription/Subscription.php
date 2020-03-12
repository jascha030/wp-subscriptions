<?php

namespace Jascha030\WPSI\Subscription;

class Subscription
{
    private $hook;

    private $method;

    private $arguments = [];

    public function __construct(string $hook, string $method, array $arguments = null)
    {
        $this->hook = $hook;

        $this->method = $method;

        if ($arguments) {
            $this->arguments = $arguments;
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
