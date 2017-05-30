<?php
/**
 * PHP Billing Library
 *
 * @link      https://github.com/hiqdev/php-billing
 * @package   php-billing
 * @license   BSD-3-Clause
 * @copyright Copyright (c) 2017, HiQDev (http://hiqdev.com/)
 */

namespace hiqdev\php\billing;

use DateTime;

/**
 * Sale.
 *
 * @author Andrii Vasyliev <sol@hiqdev.com>
 */
class Sale implements SaleInterface
{
    /**
     * @var int
     */
    public $id;

    /**
     * @var TargetInterface
     */
    public $target;

    /**
     * @var CustomerInterface
     */
    public $customer;

    /**
     * @var PlanInterface
     */
    public $plan;

    /**
     * @var DateTime
     */
    public $time;
}
