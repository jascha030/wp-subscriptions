<?php

namespace Jascha030\WP\Subscriptions\Provider;

use Jascha030\WP\Subscriptions\Shared\Container\WordpressSubscriptionContainer;

/**
 * Trait Provider
 *
 * @package Jascha030\WP\Subscriptions\Provider
 * @deprecated
 */
trait Provider
{
    /**
     * getData
     *
     * @param string $type
     *
     * @return mixed
     * @deprecated
     */
    public function getData(string $type)
    {
        trigger_error(
            static::class . ' and ' . __METHOD__ . ' are deprecated' . WordpressSubscriptionContainer::class . 'resolves provider data automatically',
            E_USER_DEPRECATED
        );

        return (WordpressSubscriptionContainer::getInstance())->getProviderData($this, $type);
    }
}
