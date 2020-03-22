<?php

namespace Jascha030\WPSI\Subscription;

/**
 * Class Shortcode
 *
 * @package Jascha030\WPSI\Subscription
 */
class Shortcode
{
    USE Hookable;

    public function add()
    {
        if (! is_callable($this->callable) && ! function_exists($this->callable)) {
            throw new \Exception("Invalid callable"); //Todo: create Exception.
        }
    }
}
