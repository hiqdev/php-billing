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

use hiqdev\php\billing\charge\ChargeModifier;
use hiqdev\php\billing\charge\FormulaChargeModifierTrait;
use hiqdev\php\billing\plan\PlanInterface;
use hiqdev\php\billing\target\TargetInterface;
use hiqdev\php\billing\type\TypeInterface;
use hiqdev\php\units\Quantity;
use hiqdev\php\units\QuantityInterface;
use Money\Money;

/**
 * Single Price.
 *
 * - no charge for quantity less then prepaid
 * - same price for any quantity above prepaid
 *
 * TODO add `$modifier` property instead of FormulaChargeModifierTrait
 *
 * @see PriceInterface
 *
 * @author Andrii Vasyliev <sol@hiqdev.com>
 */
class SinglePrice extends AbstractPrice
{
    /**
     * @var QuantityInterface prepaid quantity also implies Unit
     * XXX cannot be null cause Unit is required
     */
    protected $prepaid;

    /**
     * @var Money
     */
    protected $price;

    public function __construct(
                            $id,
        TypeInterface       $type,
        TargetInterface     $target,
        PlanInterface       $plan = null,
        QuantityInterface   $prepaid,
        Money               $price
    ) {
        parent::__construct($id, $type, $target, $plan);
        $this->prepaid  = $prepaid;
        $this->price    = $price;
    }

    public function getPrepaid()
    {
        return $this->prepaid;
    }

    public function getPrice()
    {
        return $this->price;
    }

    /**
     * {@inheritdoc}
     */
    public function calculateUsage(QuantityInterface $quantity): ?QuantityInterface
    {
        $usage = $quantity->convert($this->prepaid->getUnit())->subtract($this->prepaid);

        return $usage->isPositive() ? $usage : null;
    }

    /**
     * {@inheritdoc}
     * Same price for any usage.
     */
    public function calculatePrice(QuantityInterface $usage): ?Money
    {
        return $this->price;
    }

    public function jsonSerialize()
    {
        return array_merge(parent::jsonSerialize(), [
            'prepaid' => $this->prepaid,
            'price' => $this->price,
        ]);
    }
}
