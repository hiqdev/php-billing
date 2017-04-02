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

use hiqdev\php\units\QuantityInterface;
use Money\Money;

/**
 * Single Price:.
 *
 * - no charge for quantity less then prepaid
 * - same price for any quantity above prepaid
 *
 * @see PriceInterface
 *
 * @author Andrii Vasyliev <sol@hiqdev.com>
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
        Money               $price
    ) {
        parent::__construct($target, $type);
        $this->prepaid  = $prepaid;
        $this->price    = $price;
    }

    /**
     * {@inheritdoc}
     */
    public function calculateUsage(QuantityInterface $quantity)
    {
        $usage = $quantity->convert($this->prepaid->getUnit())->subtract($this->prepaid);

        return $usage->isPositive() ? $usage : null;
    }

    /**
     * {@inheritdoc}
     * Same price for any usage.
     */
    public function calculatePrice(QuantityInterface $usage)
    {
        return $this->price;
    }
}
