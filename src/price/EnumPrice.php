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
    /**
     * @var UnitInterface
     */
    protected $unit;

    /**
     * @var Currency
     */
    protected $currency;

    /**
     * @var array quantity => total sum for the quantity
     */
    protected $sums;

    public function __construct(
                            $id,
        TypeInterface       $type,
        TargetInterface     $target,
        PlanInterface       $plan = null,
        UnitInterface       $unit,
        Currency            $currency,
        array               $sums
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

    public function getSums()
    {
        return $this->sums;
    }

    /**
     * {@inheritdoc}
     */
    public function calculateSum(QuantityInterface $quantity): ?Money
    {
        $usage = $this->calculateUsage($quantity)->getQuantity();

        foreach ($this->sums as $value => $price) {
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

        return $sum->divide($usage->getQuantity());
    }

    /**
     * {@inheritdoc}
     */
    public function calculateUsage(QuantityInterface $quantity): ?QuantityInterface
    {
        return $quantity->convert($this->unit);
    }

    public function jsonSerialize()
    {
        return array_merge(parent::jsonSerialize(), [
            'currency' => $this->currency,
            'sums' => $this->sums,
            'unit' => $this->unit
        ]);
    }
}
