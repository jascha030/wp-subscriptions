<?php

namespace Jascha030\WPSI\Subscription;

/**
 * Class Hookable
 *
 * @package Jascha030\WPSI\Subscription
 */
trait Hookable
{
    protected $tag;

    protected $callable;

    /**
     * Hookable constructor.
     *
     * @param string $tag
     * @param callable $callable
     */
    public function __construct(string $tag, Callable $callable)
    {
        $this->tag = $tag;

        $this->callable = $callable;
    }

    /**
     * @return string
     */
    public function getTag(): string
    {
        return $this->tag;
    }

    /**
     * @return callable
     */
    public function getCallable(): callable
    {
        return $this->callable;
    }
}
