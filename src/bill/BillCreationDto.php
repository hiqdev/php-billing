<?php
/**
 * PHP Billing Library
 *
 * @link      https://github.com/hiqdev/php-billing
 * @package   php-billing
 * @license   BSD-3-Clause
 * @copyright Copyright (c) 2017-2020, HiQDev (http://hiqdev.com/)
 */

namespace hiqdev\php\billing\bill;

/**
 * @author Andrii Vasyliev <sol@hiqdev.com>
 */
class BillCreationDto
{
    public $id;

    public $type;

    public $time;

    public $sum;

    public $quantity;

    public $customer;

    public $target;

    public $requisite;

    public $plan;

    public $charges;

    public $state;

    public $usageInterval;
}
