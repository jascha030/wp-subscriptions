<?php

namespace Jascha030\WP\Subscriptions\Provider;

use Jascha030\WP\Subscriptions\Provider\Auto\ActionAutoProvider;
use Jascha030\WP\Subscriptions\Provider\Auto\FilterAutoProvider;

/**
 * Trait Provider
 *
 * @package Jascha030\WP\Subscriptions\Provider
 */
trait Provider
{
    // Todo how to extend this?
    protected $dataDefinitions = [
        ActionProvider::class     => 'actions',
        FilterProvider::class     => 'filters',
        ShortcodeProvider::class  => 'shortcodes',
        ActionAutoProvider::class => 'actions',
        FilterAutoProvider::class => 'filters',
    ];

    public function getData(string $type)
    {
        $prop = $this->dataDefinitions[$type];

        if (property_exists(static::class, $prop)) {
            return static::$$prop;
        }

        return [];
    }
}
