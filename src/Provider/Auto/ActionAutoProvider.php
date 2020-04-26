<?php

namespace Jascha030\WP\Subscriptions\Provider\Auto;

use Jascha030\WP\Subscriptions\Provider\ActionProvider;
use ReflectionException;

/**
 * Class ActionAutoProvider
 *
 * @package Jascha030\WP\Subscriptions\Provider\Auto
 */
class ActionAutoProvider extends AutoProvider implements ActionProvider
{
    /**
     * @var array
     */
    public static $actions = [];

    /**
     * ActionAutoProvider constructor.
     *
     * @param array|null $exclude
     *
     * @throws ReflectionException
     */
    public function __construct(array $exclude = null)
    {
        parent::__construct();

        self::$actions = $this->init($exclude);
    }
}
