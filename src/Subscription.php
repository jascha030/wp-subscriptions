<?php

namespace Jascha030\WP\Subscriptions;

use Exception;
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

    private const STATUS_INITIAL = 0;
    private const STATUS_ACTIVATING = 1;
    private const STATUS_ACTIVE = 2;
    private const STATUS_TERMINATING = 3;
    private const STATUS_TERMINATED = -1;

    protected $data;

    private $id;

    private $status = self::STATUS_INITIAL;

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
     * @return array
     */
    abstract public static function create(SubscriptionProvider $provider, string $context): array;

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
        return $this->status === self::STATUS_ACTIVE;
    }

    final public function activating(): bool
    {
        return $this->status === self::STATUS_ACTIVATING;
    }

    final public function status(): int
    {
        return $this->status;
    }

    /**
     * @throws SubscriptionException
     */
    final public function subscribe(): void
    {
        if ($this->active()) {
            throw new SubscriptionException("Already subscribed to {$this->id}");
        }

        $this->status = self::STATUS_ACTIVATING;
        $this->activation();
        $this->status = self::STATUS_ACTIVE;
    }

    /**
     * @throws \Jascha030\WP\Subscriptions\Exception\SubscriptionException
     */
    final public function unsubscribe(): void
    {
        if (! $this->active()) {
            throw new SubscriptionException("Already unsubscribed to {$this->id}");
        }

        $this->status = self::STATUS_TERMINATING;
        $this->termination();
        $this->status = self::STATUS_TERMINATED;
    }

    /**
     * Unhook subscribed methods
     */
    public function __destruct()
    {
        try {
            $this->termination();
        } catch (Exception $e) {
        }
    }

    abstract public function activation(): void;

    abstract public function termination(): void;
}
