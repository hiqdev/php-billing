<?php
/**
 * PHP Billing Library
 *
 * @link      https://github.com/hiqdev/php-billing
 * @package   php-billing
 * @license   BSD-3-Clause
 * @copyright Copyright (c) 2017-2018, HiQDev (http://hiqdev.com/)
 */

namespace hiqdev\php\billing\action;

/**
 * @author Andrii Vasyliev <sol@hiqdev.com>
 */
class ActionCreationDto
{
    public $id;

    public $type;

    public $target;

    public $quantity;

    public $customer;

    public $time;

    public $sale;

    public $state;

    public $parent;
}
