<?php

namespace Jascha030\WPSI\Subscriber;

/**
 * Class AjaxActionSubscriber
 *
 * @package Jascha030\WPSI\Subscriber
 */
class AjaxActionSubscriber extends ActionSubscriber
{
    const WP_AJAX = "wp_ajax_";

    const NOPRIV = "wp_ajax_nopriv_";

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

        foreach (get_class_methods($this) as $method) {
            if (! in_array($method, $this->ignoredMethods) && strpos($method,
                    "__") !== 0 && ! method_exists(SubscriberTrait::class, $method)) {
                $this->setSubscription(self::WP_AJAX . $method, $method);
                $this->setSubscription(self::NOPRIV . $method, $method);
            }
        }
    }
}
