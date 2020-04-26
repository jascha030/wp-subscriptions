# WP Subscriptions

Backbone for interfacing with the Wordpress Plugin Common API (Hooks).
Based on idea from [this article](https://carlalexander.ca/polymorphism-wordpress-interfaces/) by Carl Alexander.

## Usage

The two basic types of providers are based on the ActionProvider and the FilterProvider.
These Providers use a static property that tells the Subscription manager to hook specific methods to WP plugin hooks.

Actions:
```php
public static $actions = []; // ActionProvider interface
```

Filters:
```php
public static $filters = []; // FilterProvider interface
```

To use the subscription manager your code needs an instance of the `PluginAPI` class.
You can extend this class and use it as the base for your Wordpress Plugin.

The constructor of the `PluginAPI` class takes an array of provider classes and uses it's `create` method registers
 them to the`SubscriptionManager`.
 
```php
// PluginAPI class
protected function create($run = true)
{
    foreach ($this->providerDependencies as $provider) {
        self::registerProvider($provider); // registers providers to the SubscriptionManager
    }

    if ($run) {
        $this->run(); // Hooks the methods to their respective hooks
    }
}
```

Provider example:

```php
class ExampleProvider implements ActionProvider
{
    USE Proivder; // trait with methods used provide data to SubscriptionManager

    public static $actions = [
        'init' => 'initMethod' // Hook => method
    ];
    
    public function initMethod() // Method to be hooked to the init hook
    {
        echo "This is a method";
    }
}
```

This example shows a basic ActionProvider a class can also implement ActionProvider and FilterProvider at the same time.
