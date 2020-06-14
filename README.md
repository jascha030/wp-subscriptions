# WP Subscriptions

Backbone for interfacing with the Wordpress Plugin Common API (Hooks).
Keeping track of actions and filters and making it easy to hook or unhook class methods.

## Geting started

#### Requirements

- `composer`
- `php >= 7.1`

> This package does not keep in mind, the *Wordpress Coding Standards* and therefore is not
> compatible with `php 5.3` or any other versions before `php 7.1`

#### Installation

```shell script
composer require jascha030/wp-subscriptions
```

## Usage

#### Providers 

The two basic types of providers are the `ActionProvider` and the `FilterProvider` interfaces.
These interfaces are derivatives of the `SubscriptionProvider` interface. 

These interfaces don't require any methods to be implemented and are used by the `WordpressSubscriptionContainer` and
 other core logic to identify a class within a Wordpress plugin.
 
These Providers use a static property that tells the Subscription manager to hook specific methods to WP plugin hooks.

Actions:
```php
public static $actions = []; // ActionProvider interface
```

Filters:
```php
public static $filters = []; // FilterProvider interface
```

Provider example:

```php
class ExampleProvider implements ActionProvider
{
    public static $actions = [
        'plugins_loaded' => 'load', // Hook => method
        'pre_get_posts' => ['doQueryStuff', 10, 1], // Example with priority and number of arguments
        'wp_loaded' => [
            ['loaded'],
            ['moreLoaded']
        ], // Example of multiple methods hooked to one action hook
    ];
    
    public function load() // Method to be hooked to the plugins_loaded hook
    {
        echo "This is a method that loads stuff...";
    }

    public function doQueryStuff()
    {
        // hmmm, doing lots of querylicious stuff
    }
    
    public function loaded()
    {
        // Much load, such wow, very plugadocious
    }
    
    public function moreLoaded()
    {
        // Will it ever stop??
    }
}
```

This example shows a basic ActionProvider a class can also implement ActionProvider and FilterProvider at the same time.

#### Registering a provider

```php

$subscriptionContainer = WordpressSubscriptionContainer::getInstance(); // Get container instance

$subscriptionContainer->register(ExampleProvider::class); // Register provider

$subscriptionContainer->run(); // Hook all providers and their methods to hooks

```

## Info and inspiration 

The subscription idea provides flexibility, so you don't have to overuse the singleton pattern in OOP wordpress
 plugins. Now you are not restricted to extending classes for every other instance you need (for example: when you
  build a post type class you can create a config with post types you can loop trough instead of having to make
   separate classes for each post type).

Based on idea from [this article](https://carlalexander.ca/polymorphism-wordpress-interfaces/) by Carl Alexander.
