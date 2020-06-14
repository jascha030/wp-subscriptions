<?php

namespace Jascha030\WP\Subscriptions\Exception;

use Jascha030\WP\Subscriptions\Provider\SubscriptionProvider;
use Throwable;

/**
 * Class DoesNotImplementSubscriberException
 *
 * @package Jascha030\WP\Subscriptions\Exception
 */
class DoesNotImplementProviderException extends InvalidClassException
{
    public function __construct(string $class, $code = 0, Throwable $previous = null)
    {
        $shouldImplement = SubscriptionProvider::class;
        parent::__construct("{$class} does not implement {$shouldImplement}", $code, $previous);
    }
}
