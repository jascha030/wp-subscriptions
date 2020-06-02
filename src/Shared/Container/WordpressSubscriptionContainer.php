<?php

namespace Jascha030\WP\Subscriptions\Shared\Container;

use Jascha030\WP\Subscriptions\Exception\DoesNotImplementProviderException;
use Jascha030\WP\Subscriptions\Manager\ItemTypes;
use Jascha030\WP\Subscriptions\Provider\SubscriptionProvider;
use Jascha030\WP\Subscriptions\Shared\DataConfig;

class WordpressSubscriptionContainer extends Container
{
    /**
     * @var array|\Jascha030\WP\Subscriptions\Shared\DataConfig
     */
    protected $dataConfig = [];

    protected $providerBindings = [];

    protected $subscriptions = [];

    protected $failed = [];

    public function __construct()
    {
        $this->dataConfig = new DataConfig();
    }

    public function getDefinition(int $type, string $key)
    {
        switch ($type) {
            case DataConfig::SUBSCRIPTION:
                return $this->dataConfig->getSubscriptionTypes()[$key] ?? false;
                break;
            case DataConfig::PROPERTY:
                return $this->dataConfig->getProviderDataProperties()[$key] ?? false;
                break;
            case DataConfig::CREATION_METHOD:
                return $this->dataConfig->getProviderMethods()[$key] ?? false;
                break;
            default:
                return false;
                break;
        }
    }

    public function run()
    {
        $this->createSubscriptionsFromProvidedData();

        foreach ($this->subscriptions as &$subscription) {
            try {
                $subscription->subscribe();
            } catch (\Exception $exception) {
                $this->failed[$subscription->getUuid()] = $exception->getMessage();
            }
        }
    }

    public function bind(string $abstract, $concrete = null, $shared = false)
    {
        if (is_subclass_of($abstract, SubscriptionProvider::class) && ! in_array($abstract, $this->providerBindings)) {
            $this->providerBindings[] = $abstract;
        }

        parent::bind($abstract, $concrete, $shared = false);
    }

    public function register($abstract, $provider = null)
    {
        if (is_object($abstract) && ! $provider) {
            $provider = $abstract;
            $abstract = get_class($provider);
        }

        if (! is_subclass_of($abstract, SubscriptionProvider::class)) {
            throw new DoesNotImplementProviderException();
        }

        if (! $this->bound($abstract)) {
            $this->bind($abstract, $provider);
        }
    }

    public function registerSubscriptionType(string $providerClass, string $dataClass, $creationMethod = null)
    {
        /**
         * Todo: implement
         *
         * Need provider class
         * Need data / subscription class
         * Need creation method in form of Factory class / Callable / null = dataclass without constructor
         */
    }

    protected function createSubscriptionsFromProvidedData()
    {
        foreach ($this->providerBindings as $abstract) {
            $binding = $this->bindings[$abstract];

            if (is_object($binding['concrete'])) {
                $abstract = $binding['concrete'];
            }

            foreach ($this->dataConfig->getProviderMethods() as $type => $method) {
                $this->subscriptions = array_merge($this->subscriptions, $this->createSubscriptions($abstract, $type));
            }
        }
    }

    protected function createSubscriptions($provider, string $type)
    {
        $creationMethod = $this->getDefinition(DataConfig::CREATION_METHOD, $type);
        if (! $creationMethod) {
            return [];
        }

        if (! $this->bound($creationMethod)) {
            $this->bind($creationMethod);
        }

        if (is_string($provider)) {
            $provider = $this->resolve($provider);
        }

        return ($this->resolve($creationMethod))->create($provider, ['type' => $type]);
    }

    public function getProviderData(SubscriptionProvider $provider, $type)
    {
        $prop = $this->getDefinition(DataConfig::PROPERTY, $type);

        if (property_exists(get_class($provider), $prop)) {
            return $provider::$$prop;
        }

        return [];
    }

    public function getList(int $type = ItemTypes::PROVIDERS)
    {
        $data = [];

        switch ($type) {
            case ItemTypes::PROVIDERS:
                $data = $this->providerBindings;
                break;
            case ItemTypes::SUBSCRIPTIONS:
                foreach ($this->subscriptions as $subscription) {
                    $data[$subscription->getUuid()] = $subscription->info();
                }
                break;
            case ItemTypes::FAILED_SUBSCRIPTIONS:
                $data = $this->failed;
                break;
            default:
                return null;
                break;
        }

        $list = [];
        foreach ($data as $key => $item) {
            $list[$key] = (is_object($item)) ? $item : get_class($item);
        }

        return $list;
    }
}

\class_alias(WordpressSubscriptionContainer::class, \Jascha030\WP\Subscriptions\Manager\SubscriptionManager::class);
