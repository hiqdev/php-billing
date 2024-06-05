<?php
/**
 * PHP Billing Library
 *
 * @link      https://github.com/hiqdev/php-billing
 * @package   php-billing
 * @license   BSD-3-Clause
 * @copyright Copyright (c) 2017-2020, HiQDev (http://hiqdev.com/)
 */

namespace hiqdev\php\billing\sale;

/**
 * @author Andrii Vasyliev <sol@hiqdev.com>
 */
class SaleCreationDto
{
    public $id;

    public $target;

    public $customer;

    public $plan;

    public $time;

    public $closeTime;

    public $data;

    public $reason;
}
