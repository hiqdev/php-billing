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
 */
interface PriceInterface
{
    /**
     * Calculate charge for given action.
     * @param ActionInterface $action
     * @return ChargeInterface
     */
    public function calculateCharge(QuantityInterface $quantity);

    /**
     * Calculate usage for given quantity.
     * @param QuantityInterface $quantity
     * @return QuantityInterface
     */
    public function calculateUsage(QuantityInterface $quantity);

    /**
     * Calculate price for given usage.
     * @param QuantityInterface $usage
     * @return MoneyInterface
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
