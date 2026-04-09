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
use Money\Money;

/**
 * Rate Price:
 * - holds rate in percents
 * e.g. rate for referral payments calculation
 * @see PriceInterface
 *
 * @author Andrii Vasyliev <sol@hiqdev.com>
 */
class RatePrice extends AbstractPrice implements PriceWithRateInterface
{
    public function __construct(
        $id,
        TypeInterface $type,
        TargetInterface $target,
        ?PlanInterface $plan,
        protected float $rate
    ) {
        parent::__construct($id, $type, $target, $plan);
    }

    public function getRate(): float
    {
        return $this->rate;
    }

    #[\Override]
    public function calculateSum(QuantityInterface $quantity): ?Money
    {
        $sum = $quantity->multiply((string) -$this->rate);
        $currency = strtoupper($sum->getUnit()->getName());

        return Money::{$currency}((int)floor($sum->getQuantity()));
    }

    public function calculatePrice(QuantityInterface $quantity): ?Money
    {
        $sum = $this->calculateSum($quantity);
        if (!$sum instanceof Money) {
            return null;
        }

        $usage = $this->calculateUsage($quantity);
        if (!$usage instanceof QuantityInterface) {
            return null;
        }

        return $sum->divide(sprintf('%.14F', $usage->getQuantity()));
    }

    public function calculateUsage(QuantityInterface $quantity): ?QuantityInterface
    {
        return $quantity;
    }
}
