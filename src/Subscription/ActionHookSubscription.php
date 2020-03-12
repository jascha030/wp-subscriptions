<?php

namespace Jascha030\WPSI\Subscription;

/**
 * Class ActionHookSubscription
 *
 * @package Jascha030\WPSI\Subscription
 */
class ActionHookSubscription extends HookSubscription
{
    private $method = SubscriptionMethodTypes::ACTION;

    /**
     * @return mixed|string
     */
    public function getMethod()
    {
        return $this->method;
    }
}
