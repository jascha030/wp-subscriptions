<?php

namespace Jascha030\WP\Subscriptions\Shared;

abstract class Singleton
{
    protected static $instance;

    public static function getInstance()
    {
        if (is_null(static::$instance)) {
            static::$instance = new static;
        }

        return static::$instance;
    }
}
