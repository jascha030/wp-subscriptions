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
        $implements = class_implements(static::class);

        if (! in_array($type, $implements) || ! array_key_exists($type, $this->dataDefinitions)) {
            throw new \Exception();
        }

        if (! property_exists($type, $this->dataDefinitions['type'])) {
            return [];
        }

        $prop = $this->dataDefinitions[$type];

        return static::$$prop;
    }
}
