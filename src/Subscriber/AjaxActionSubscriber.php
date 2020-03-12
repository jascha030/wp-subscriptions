<?php

namespace Jascha030\WPSI\Subscriber;

class AjaxActionSubscriber implements ActionSubscriber
{
    const WP_AJAX = "wp_ajax_";

    const NOPRIV = "wp_ajax_nopriv_";

    USE Subscriber;

    protected static $actions = [];

    private $ignoredMethods = [];

    public function __construct($ignore = [])
    {
        if (! empty($ignore)) {
            $this->ignoredMethods = array_merge($this->ignoredMethods, $ignore);
        }

        $methods = get_class_methods($this);

        foreach ($methods as $method) {
            if (! in_array($method, $this->ignoredMethods) && $method !== "__construct" && ! method_exists($method,
                    Subscriber::class)) {

                self::$actions[self::WP_AJAX] = [$this, $method];
                self::$actions[self::NOPRIV]  = [$this, $method];
            }
        }
    }
}
