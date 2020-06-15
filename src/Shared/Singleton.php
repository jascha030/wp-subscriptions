<?php

namespace Jascha030\WP\Subscriptions\Shared;

class Singleton
{
    protected static $instance;

    final public static function getInstance()
    {
        if (is_null(static::$instance)) {
            static::$instance = new static();
        }

        return static::$instance;
    }

    /**
     * Oi mate! In a galaxy far far away cloned men sound like they are from New Zealand...
     *
     * This is illegal therefore declared private, don't make me call in the feds
     */
    private function __clone()
    {
    }

    /**
     * grabyabrushandputalittlemakeup... hidethescarstofadeawaytheshakup...
     *
     * This is illegal therefore declared private, don't make me call in the feds
     */
    private function __wakeup()
    {
    }
}
