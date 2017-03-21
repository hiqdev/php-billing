<?php
/**
 * PHP Billing Library
 *
 * @link      https://github.com/hiqdev/php-billing
 * @package   php-billing
 * @license   BSD-3-Clause
 * @copyright Copyright (c) 2017, HiQDev (http://hiqdev.com/)
 */

namespace hiqdev\php\billing;

/**
 * Single Price:
 * - no charge for quantity less then prepaid
 * - same price for any quantity above prepaid
 * @see PriceInterface
 */
class SinglePrice extends AbstractPrice
{
    /**
     * @var Quantity prepaid quantity also implies Unit
     */
    protected $prepaid;

    /**
     * @var Money
     */
    protected $price;

    public function __construct(
        TargetInterface     $target,
        TypeInterface       $type,
        QuantityInterface   $prepaid,
        MoneyInterface      $price
    ) {
        parent::__construct($target, $type);
        $this->prepaid  = $prepaid;
        $this->price    = $price;
    }

    /**
     * Calculate usage for given quantity.
     * @param QuantityInterface $quantity
     * @return QuantityInterface|null quantity in proper unit, null when not chargable
     */
    public function calculateUsage(QuantityInterface $quantity)
    {
        $diff = $quantity->convert($this->prepaid)->subtract($this->prepaid);

        return $diff->isPositive() ? $diff : null;
    }

    /**
     * Calculate price for given usage.
     * @param QuantityInterface $usage
     * @return MoneyInterface|null null when not chargable
     */
    public function calculatePrice(QuantityInterface $usage)
    {
        return $this->price;
    }
}
