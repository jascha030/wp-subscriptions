<?php

namespace Jascha030\WP\Subscriptions\Factory;

interface Factory
{
    public function create($provider, array $arguments = []);
}