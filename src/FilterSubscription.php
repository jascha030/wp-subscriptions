<?php

namespace Jascha030\WP\Subscriptions;

use Jascha030\WP\Subscriptions\Exception\InvalidArgumentException;
use Jascha030\WP\Subscriptions\Exception\SubscriptionException;
use Jascha030\WP\Subscriptions\Provider\SubscriptionProvider;

use function Jascha030\WP\Subscriptions\Shared\Container\WPSC;

/**
 * Class FilterSubscription
 *
 * @package Jascha030\WP\Subscriptions
 */
class FilterSubscription extends Subscription
{
    protected const CONTEXT = 'filter';

    protected $called = 0;

    protected $tagId;

    protected $callable;

    /**
     * @param \Jascha030\WP\Subscriptions\Provider\SubscriptionProvider $provider
     * @param $context
     *
     * @return array
     * @throws \Jascha030\WP\Subscriptions\Exception\InvalidArgumentException
     * @throws \Exception
     */
    public static function create(SubscriptionProvider $provider, $context): array
    {
        $subscriptions = [];
        $data          = WPSC()->getProviderData($provider, $context);

        foreach ($data as $tag => $parameters) {
            if (is_array($parameters) && is_array($parameters[0])) {
                foreach ($parameters as $params) {
                    $subscription = self::createSubscriptionObject($provider, $tag, $params, $context);

                    $subscriptions[] = $subscription;
                }
            } else {
                $subscription = self::createSubscriptionObject($provider, $tag, $parameters, $context);

                $subscriptions[] = $subscription;
            }
        }

        return ! empty($subscriptions) ? $subscriptions : [];
    }

    /**
     * @param \Jascha030\WP\Subscriptions\Provider\SubscriptionProvider $provider
     * @param $tag
     * @param $parameters
     * @param $context
     *
     * @return mixed
     * @throws \Jascha030\WP\Subscriptions\Exception\InvalidArgumentException
     */
    private static function createSubscriptionObject(
        SubscriptionProvider $provider,
        string $tag,
        $parameters,
        string $context
    ): Subscription {
        $callable = [$provider, is_array($parameters) ? $parameters[0] : $parameters];

        if (! is_callable($callable)) {
            throw new InvalidArgumentException('variable is not a valid callable');
        }

        $priority          = (is_array($parameters)) ? $parameters[1] ?? 10 : 10;
        $acceptedArguments = (is_array($parameters)) ? $parameters[2] ?? 1 : 1;

        $subscription = new $context();
        $subscription->set(compact('tag', 'callable', 'priority', 'acceptedArguments'));

        return $subscription;
    }

    final public function timesRan(): int
    {
        return $this->called;
    }

    final public function ran(): bool
    {
        return $this->called > 0;
    }

    final public function activation(): void
    {
        if (! $this->activating()) {
            throw new SubscriptionException(static::class . 'Should not be called directly');
        }

        $this->tagId = $this->getId() . '_' . $this->data['tag'];
        $callable    = $this->data['callable'];

        $this->data['callable'] = function () use ($callable) {
            $this->add($this->tagId, $callable)->do($this->tagId)->called++;
        };

        $this->add(...array_values($this->data));
    }

    final public function termination(): void
    {
        $this->removeAll($this->tagId)->remove(...array_values($this->data));
    }

    /**
     * @param string $tag
     * @param callable $callable
     * @param int $prio
     * @param int $acceptedArgs
     *
     * @return $this
     */
    private function add(string $tag, callable $callable, int $prio = 10, int $acceptedArgs = 1): FilterSubscription
    {
        $method = 'add_' . static::CONTEXT;
        $method($tag, $callable, $prio, $acceptedArgs);

        return $this;
    }

    /**
     * @param string $tag
     *
     * @return $this
     */
    private function do(string $tag): FilterSubscription
    {
        $method = 'do_' . static::CONTEXT;
        $method($tag);

        return $this;
    }

    private function remove(string $tag, callable $callable, int $prio = 10, int $acceptedArgs = 1): FilterSubscription
    {
        $method = 'remove_' . static::CONTEXT;
        $method($tag, $callable, $prio, $acceptedArgs);

        return $this;
    }

    private function removeAll(string $tag): FilterSubscription
    {
        $method = 'remove_all_' . static::CONTEXT . 's';
        $method($tag);

        return $this;
    }
}
