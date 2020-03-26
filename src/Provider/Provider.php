<?php

namespace Jascha030\WPOL\Subscription\Provider;

/**
 * Trait Provider
 *
 * @package Jascha030\WPOL\Subscription\Provider
 */
trait Provider
{
    /**
     * @return array|bool
     */
    public static function getActions()
    {
        $class = get_called_class();

        return (in_array(ActionProvider::class, class_implements($class)) && property_exists($class,
                'actions')) ? $class::$actions : false;
    }

    /**
     * @return array|bool
     */
    public static function getFilters()
    {
        $class = get_called_class();

        return (in_array(FilterProvider::class, class_implements($class)) && property_exists($class,
                'filters')) ? $class::$filters : false;
    }

    /**
     * @return array|bool
     */
    public static function getShortcodes()
    {
        $class = get_called_class();

        return (in_array(ShortcodeProvider::class, class_implements($class)) && property_exists($class,
                'shortcodes')) ? $class::$shortcodes : false;
    }
}
