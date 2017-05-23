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
 * Bill.
 *
 * @author Andrii Vasyliev <sol@hiqdev.com>
 */
class Bill
{
    /**
     * @var integer
     */
    public $id;

    /**
     * @var Type
     */
    public $type;

    /**
     * @var Purse
     */
    public $purse;

    /**
     * @var object
     */
    public $object;

    /**
     * @var Plan
     */
    public $plan;

    /**
     * @var DateTime
     */
    public $time;

    /**
     * @var double
     */
    public $quantity;

    /**
     * @var integer
     */
    public $sum;

    /**
     * @var bool
     */
    public $isFinished;

    /**
     * @var resource[]
     */
    public $resources = [];
}
