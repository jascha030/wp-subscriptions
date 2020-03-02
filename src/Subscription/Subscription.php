<?php

namespace Jascha030\WPSI\Subscription;

class Subscription
{
    private $name;

    private $method;

    private $invokerMethod;

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
    public function getInvokerMethod()
    {
        return $this->invokerMethod;
    }
}
