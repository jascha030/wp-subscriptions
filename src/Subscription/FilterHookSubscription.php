<?php

namespace Jascha030\WPSI\Subscription;

class FilterHookSubscription extends HookSubscription
{
    private $method = SubscriptionMethodTypes::FILTER;

    /**
     * @return string
     */
    public function getMethod()
    {
        return $this->method;
    }
}
