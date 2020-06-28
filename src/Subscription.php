<?php

namespace Jascha030\WP\Subscriptions;

use Jascha030\WP\Subscriptions\Exception\SubscriptionException;
use Jascha030\WP\Subscriptions\Provider\SubscriptionProvider;

/**
 * Class Subscription
 *
 * @package Jascha030\WP\Subscriptions
 */
abstract class Subscription extends PropertyOverload
{
    protected const ID_PREFIX = 'wpsc_';

    protected $data;

    private $id;

    private $active = false;

    final public function __construct()
    {
        $this->id = uniqid(static::ID_PREFIX, true);
    }

    /**
     * Implement Factory method
     *
     * @param \Jascha030\WP\Subscriptions\Provider\SubscriptionProvider $provider
     * @param $context
     *
     * @return mixed
     */
    abstract public static function create(SubscriptionProvider $provider, $context);

    final public function set(array $data): void
    {
        $this->data = $data;
    }

    final public function getId(): string
    {
        return $this->id;
    }

    final public function active(): bool
    {
        return $this->active;
    }

    /**
     * @throws SubscriptionException
     */
    final public function subscribe(): void
    {
        if ($this->active()) {
            throw new SubscriptionException("Already subscribed to {$this->id}");
        }

        $this->active = true;
        $this->activation();
    }

    /**
     * @throws \Jascha030\WP\Subscriptions\Exception\SubscriptionException
     */
    final public function unsubscribe(): void
    {
        if (! $this->active()) {
            throw new SubscriptionException("Already unsubscribed to {$this->id}");
        }

        $this->active = false;
        $this->termination();
    }

    /**
     * Unhook subscribed methods
     */
    public function __destruct()
    {
        try {
            $this->termination();
        } catch (\Exception $e) {
        }
    }

    abstract protected function activation(): void;

    abstract protected function termination(): void;
}
