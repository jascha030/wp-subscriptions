<?php

namespace Jascha030\WP\Subscriptions\Provider\Auto;

use Jascha030\WP\Subscriptions\Provider\FilterProvider;
use ReflectionException;

/**
 * Class FilterAutoProvider
 *
 * @package Jascha030\WP\Subscriptions\Provider\Auto
 */
class FilterAutoProvider extends AutoProvider implements FilterProvider
{
    /**
     * @var array
     */
    public static $filters = [];

    /**
     * FilterAutoProvider constructor.
     *
     * @param array|null $exclude
     *
     * @throws ReflectionException
     */
    public function __construct(array $exclude = null)
    {
        parent::__construct();
        self::$filters = $this->init($exclude);
    }
}
