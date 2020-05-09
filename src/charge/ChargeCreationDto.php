<?php
/**
 * PHP Billing Library
 *
 * @link      https://github.com/hiqdev/php-billing
 * @package   php-billing
 * @license   BSD-3-Clause
 * @copyright Copyright (c) 2017-2020, HiQDev (http://hiqdev.com/)
 */

namespace hiqdev\php\billing\charge;

/**
 * @author Andrii Vasyliev <sol@hiqdev.com>
 */
class ChargeCreationDto
{
    public $id;

    public $type;

    public $target;

    public $action;

    public $price;

    public $usage;

    public $sum;

    public $bill;

    public $state;

    public $comment;

    public $parent;
}
