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
 * Chargeable Action Interface.
 *
 * Action knows which Prices are applicable.
 * Actions form hierarchy.
 * Action implies type.
 *
 * @author Andrii Vasyliev <sol@hiqdev.com>
 */
interface ActionInterface
{
    /**
     * Returns if the given price applicable to this action.
     * @param PriceInterface $price
     * @return bool
     */
    public function isApplicable(PriceInterface $price);

    /**
     * Calculate charge for given price.
     * @param PriceInterface $action
     * @return ChargeInterface
     */
    public function calculateCharge(PriceInterface $quantity);

    /**
     * Returns client ot this action.
     * @return ClientInterface
     */
    public function getClient()
    {
        return $this->client;
    }

    /**
     * Returns target ot this action.
     * @return TargetInterface
     */
    public function getTarget()
    {
        return $this->target;
    }

    /**
     * Returns quantity ot this action.
     * @return QuantityInterface
     */
    public function getQuantity()
    {
        return $this->quantity;
    }

    /**
     * Returns time ot this action.
     * @return DateTime
     */
    public function getTime()
    {
        return $this->time;
    }
}
