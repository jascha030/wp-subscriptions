<?php

namespace Jascha030\WPSI\Subscription;

class ActionHookSubscription extends HookSubscription
{
    private $method = SubscriptionMethodTypes::ACTION;

    public function getMethod()
    {
        return $this->method;
    }
}
