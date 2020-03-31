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

        if (in_array(ActionProvider::class, class_implements($class)) && property_exists($class, 'actions')) {
            return $class::$actions;
        }
    }

    /**
     * @return array|bool
     */
    public static function getFilters()
    {
        $class = get_called_class();

        if (in_array(FilterProvider::class, class_implements($class)) && property_exists($class, 'filters')) {
            return $class::$filters;
        }
    }

    /**
     * @return array|bool
     */
    public static function getShortcodes()
    {
        $class = get_called_class();

        if (in_array(ShortcodeProvider::class, class_implements($class)) && property_exists($class,
                'shortcodes')) {
            return $class::$shortcodes;
        }
    }
}