<?php
/**
 * PHP Billing Library
 *
 * @link      https://github.com/hiqdev/php-billing
 * @package   php-billing
 * @license   BSD-3-Clause
 * @copyright Copyright (c) 2017, HiQDev (http://hiqdev.com/)
 */

namespace hiqdev\php\billing\price;

use hiqdev\php\billing\target\TargetInterface;
use hiqdev\php\billing\type\TypeInterface;
use hiqdev\php\units\QuantityInterface;
use hiqdev\php\units\UnitInterface;
use Money\Currency;
use Money\Money;

/**
 * Enum Price:
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
     * @var MoneyInterface[] amount => price
     */
    protected $prices;

    public function __construct(
                            $id,
        TypeInterface       $type,
        TargetInterface     $target,
        UnitInterface       $unit,
        Currency            $currency,
        array               $prices
    ) {
        parent::__construct($id, $type, $target);
        $this->unit = $unit;
        $this->currency = $currency;
        $this->prices = $prices;
    }

    public function getUnit()
    {
        return $this->unit;
    }

    public function getPrices()
    {
        return $this->prices;
    }

    /**
     * {@inheritdoc}
     */
    public function calculatePrice(QuantityInterface $quantity)
    {
        $usage = (string) $this->calculateUsage($quantity)->getQuantity();
        foreach ($this->prices as $value => $price) {
            if ((string) $value === (string) $usage) {
                return new Money($price, $this->currency);
            }
        }

        throw new FailedCalculatePriceException('not enumed quantity: ' . $usage);
    }

    /**
     * {@inheritdoc}
     */
    public function calculateUsage(QuantityInterface $quantity)
    {
        return $quantity->convert($this->unit);
    }
}
