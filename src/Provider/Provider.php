<?php

namespace Jascha030\WPSI\Provider;

use Jascha030\WPSI\Provider\StaticProvider\StaticActionProvider;
use Jascha030\WPSI\Provider\StaticProvider\StaticFilterProvider;

/**
 * Trait Provider
 *
 * @package Jascha030\WPSI\Provider
 */
trait Provider
{
    /**
     * @return array|bool
     */
    public function getActions()
    {
        return (in_array(ActionProvider::class, class_implements($this)) && property_exists($this,
                'actions')) ? $this->actions : false;
    }

    /**
     * @return array|bool
     */
    public function getFilters()
    {
        return (in_array(FilterProvider::class, class_implements($this)) && property_exists($this,
                'filters')) ? $this->filters : false;
    }

    /**
     * @return array|bool
     */
    public function getShortcodes()
    {
        return (in_array(ShortcodeProvider::class, class_implements($this)) && property_exists($this,
                'shortcodes')) ? $this->shortcodes : false;
    }

    /**
     * @return array|bool
     */
    public static function getStaticActions()
    {
        $class = get_called_class();

        return (in_array(StaticActionProvider::class, class_implements($class)) && property_exists($class,
                'actions')) ? $class::$actions : false;
    }

    /**
     * @return array|bool
     */
    public static function getStaticFilters()
    {
        $class = get_called_class();

        return (in_array(StaticFilterProvider::class, class_implements($class)) && property_exists($class,
                'actions')) ? $class::$filters : false;
    }
}
