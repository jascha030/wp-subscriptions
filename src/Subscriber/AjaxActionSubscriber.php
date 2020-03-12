<?php

namespace Jascha030\WPSI\Subscriber;

class AjaxActionSubscriber implements ActionSubscriber
{
    USE Subscriber;
    
    const WP_AJAX = "wp_ajax_";

    const NOPRIV = "wp_ajax_nopriv_";

    protected static $actions = [];

    protected $ignoredMethods = [];

    private $enforceAdminPrivilege;

    public function __construct($ignore = [], $enforceAdminPrivilege = false)
    {
        $this->ignoredMethods = array_merge($this->ignoredMethods, $ignore);

        $this->enforceAdminPrivilege = $enforceAdminPrivilege;

        $methods = get_class_methods($this);

        foreach ($methods as $method) {
            if (! in_array($method, $this->ignoredMethods) && ! strpos($method, "__") && ! method_exists($method,
                    Subscriber::class)) {

                self::$actions[self::WP_AJAX] = [$this, $method];
                self::$actions[self::NOPRIV]  = [$this, $method];
            }
        }
    }
}
