<?php

namespace Jascha030\WP\Subscriptions\Factory;

interface SubscriptionFactory
{
    public function create($provider, array $arguments = []);
}