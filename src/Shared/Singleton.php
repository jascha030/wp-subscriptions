<?php

namespace Jascha030\WP\Subscriptions\Shared;

class Singleton
{
    protected static $instance;

    final public static function getInstance(): Singleton
    {
        if (is_null(static::$instance)) {
            static::$instance = new static();
        }

        return static::$instance;
    }

    /**
     * Oi mate! In a galaxy far far away cloned men sound like they are from New Zealand...
     */
    private function __clone()
    {
    }

    /**
     * grabyabrushandputalittlemakeup... hidethescarstofadeawaytheshakup...
     */
    private function __wakeup()
    {
    }
}
