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
     * __clone
     * Oi mate! In a galaxy far far away cloned men sound like they are from New Zealand...
     *
     * This is illegal therefore declared private
     */
    private function __clone()
    {
    }

    /**
     * __wakeup
     * grabyabrushandputonalittlemakeup... hidethescarsandfadeawaytheshakup...
     *
     * This is illegal therefore declared private
     */
    private function __wakeup()
    {
    }
}
