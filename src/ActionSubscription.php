<?php

namespace Jascha030\WP\Subscriptions;

/**
 * Class ActionHookSubscription
 *
 * @package Jascha030\WP\Subscriptions
 */
class ActionSubscription extends FilterSubscription
{
    protected const CONTEXT = 'action';
}
