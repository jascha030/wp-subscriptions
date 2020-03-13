<?php

namespace Jascha030\WPSI\Subscriber;

/**
 * Class AjaxActionSubscriber
 *
 * @package Jascha030\WPSI\Subscriber
 */
class AjaxActionSubscriber implements ActionSubscriber
{
    USE SubscriberTrait;

    const WP_AJAX = "wp_ajax_";

    const NOPRIV = "wp_ajax_nopriv_";

    protected static $actions = [];

    protected $ignoredMethods = [];

    private $enforceAdminPrivilege;

    /**
     * AjaxActionSubscriber constructor.
     *
     * @param array $ignore
     * @param bool $enforceAdminPrivilege
     */
    public function __construct($ignore = [], $enforceAdminPrivilege = false)
    {
        $this->ignoredMethods = array_merge($this->ignoredMethods, $ignore);

        $this->enforceAdminPrivilege = $enforceAdminPrivilege;

        $methods = get_class_methods($this);

        foreach ($methods as $method) {
            if (! in_array($method, $this->ignoredMethods) && strpos($method,
                    "__") !== 0 && ! method_exists(SubscriberTrait::class, $method)) {
                $this->addAction(self::WP_AJAX . $method, $method);
                $this->addAction(self::NOPRIV . $method, $method);
            }
        }
    }
}
