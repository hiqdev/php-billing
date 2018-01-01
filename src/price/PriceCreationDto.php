<?php
/**
 * PHP Billing Library
 *
 * @link      https://github.com/hiqdev/php-billing
 * @package   php-billing
 * @license   BSD-3-Clause
 * @copyright Copyright (c) 2017-2018, HiQDev (http://hiqdev.com/)
 */

namespace hiqdev\php\billing\price;

use hiqdev\php\billing\type\TypeInterface;

/**
 * @author Andrii Vasyliev <sol@hiqdev.com>
 */
class PriceCreationDto
{
    public $id;

    /**
     * @var TypeInterface
     */
    public $type;

    public $target;

    public $plan;

    public $prepaid;

    public $price;

    public $unit;

    public $currency;

    public $sums;
}
