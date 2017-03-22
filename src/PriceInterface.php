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
 * Price Interface.
 * Tariff consists of prices.
 *
 * Knows how to calculate usage, price and sum.
 *
 * @author Andrii Vasyliev <sol@hiqdev.com>
 */
interface PriceInterface
{
    /**
     * Calculate charge for given action.
     * @param ActionInterface $action
     * @return ChargeInterface
     */
    public function calculateCharge(ActionInterface $action);

    /**
     * Calculate sum for given usage.
     * @param QuantityInterface $usage
     * @return MoneyInterface|null null when must not be charged
     */
    public function calculateSum(QuantityInterface $quantity);

    /**
     * Calculate usage for given quantity.
     * @param QuantityInterface $quantity
     * @return QuantityInterface|null null when must not be charged
     */
    public function calculateUsage(QuantityInterface $quantity);

    /**
     * Calculate price for given usage.
     * @param QuantityInterface $usage
     * @return MoneyInterface|null null when must not be charged
     */
    public function calculatePrice(QuantityInterface $usage);

    /**
     * Get target.
     * @return TargetInterface
     */
    public function getTarget();

    /**
     * Get type.
     * @return TypeInterface
     */
    public function getType();
}
