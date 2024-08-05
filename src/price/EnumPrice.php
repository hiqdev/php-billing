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
 * Enum Price:
 * - holds sums list: amount => total sum for the quantity NOT price per unit
 * - listed quantities only else exception.
 * @see PriceInterface
 *
 * @author Andrii Vasyliev <sol@hiqdev.com>
 */
class EnumPrice extends AbstractPrice
{
    protected UnitInterface $unit;

    protected Currency $currency;

    protected Sums $sums;

    public function __construct(
        $id,
        TypeInterface $type,
        TargetInterface $target,
        ?PlanInterface $plan,
        UnitInterface $unit,
        Currency $currency,
        Sums $sums,
    ) {
        parent::__construct($id, $type, $target, $plan);
        $this->unit = $unit;
        $this->currency = $currency;
        $this->sums = $sums;
    }

    public function getUnit()
    {
        return $this->unit;
    }

    public function getCurrency()
    {
        return $this->currency;
    }

    public function getSums(): Sums
    {
        return $this->sums;
    }

    /**
     * {@inheritdoc}
     */
    public function calculateSum(QuantityInterface $quantity): ?Money
    {
        $usage = $this->calculateUsage($quantity)->getQuantity();

        foreach ($this->sums->values() as $value => $price) {
            if ((string) $value === (string) $usage) {
                return new Money($price, $this->currency);
            }
        }

        throw new FailedCalculatePriceException('not enumed quantity: ' . $usage);
    }

    /**
     * {@inheritdoc}
     */
    public function calculatePrice(QuantityInterface $quantity): ?Money
    {
        $sum = $this->calculateSum($quantity);
        if ($sum === null) {
            return null;
        }

        $usage = $this->calculateUsage($quantity);
        if ($usage === null) {
            return null;
        }

        return $sum->divide(sprintf('%.14F', $usage->getQuantity()));
    }

    /**
     * {@inheritdoc}
     */
    public function calculateUsage(QuantityInterface $quantity): ?QuantityInterface
    {
        return $quantity->convert($this->unit);
    }
}
