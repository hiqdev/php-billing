<?php
/**
 * PHP Billing Library
 *
 * @link      https://github.com/hiqdev/php-billing
 * @package   php-billing
 * @license   BSD-3-Clause
 * @copyright Copyright (c) 2017-2020, HiQDev (http://hiqdev.com/)
 */

namespace hiqdev\php\billing\price;

use hiqdev\php\billing\plan\PlanInterface;
use hiqdev\php\billing\target\TargetInterface;
use hiqdev\php\billing\type\TypeInterface;
use hiqdev\php\units\QuantityInterface;
use hiqdev\php\units\UnitInterface;
use Money\Currency;
use Money\Money;

/**
 * @author Andrii Vasyliev <sol@hiqdev.com>
 */
class PriceCreationDto
{
    /**
     * @var string
     */
    public $id;

    /**
     * @var TypeInterface
     */
    public $type;

    /**
     * @var TargetInterface
     */
    public $target;

    /**
     * @var PlanInterface
     */
    public $plan;

    /**
     * @var QuantityInterface
     */
    public $prepaid;

    /**
     * @var Money
     */
    public $price;

    /**
     * @var UnitInterface
     */
    public $unit;

    /**
     * @var Currency
     */
    public $currency;

    /**
     * @var string[]
     */
    public $sums;

    /** @var float */
    public $rate;

    /** @var ProgressivePriceThreshold[]  */
    public array $thresholds = [];
}
