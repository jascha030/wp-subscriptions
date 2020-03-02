<?php

namespace Jascha030\WPSI\Subscription;

class Subscription
{
    private $name;

    private $method;

    private $arguments = [];

    private $invokerMethod;

    public function __construct(string $name, string $method, $arguments = null)
    {
        $this->name = $name;

        $this->method = $method;

        if ($arguments) {
            $this->arguments = $arguments;
        }
    }

    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return mixed
     */
    public function getMethod()
    {
        return $this->method;
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
    public function getInvokerMethod()
    {
        return $this->invokerMethod;
    }
}
