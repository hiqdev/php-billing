<?php
/**
 * PHP Billing Library
 *
 * @link      https://github.com/hiqdev/php-billing
 * @package   php-billing
 * @license   BSD-3-Clause
 * @copyright Copyright (c) 2017-2020, HiQDev (http://hiqdev.com/)
 */

namespace hiqdev\php\billing\order;

use hiqdev\php\billing\action\ActionInterface;
use hiqdev\php\billing\charge\ChargeInterface;
use hiqdev\php\billing\plan\PlanInterface;
use hiqdev\php\billing\price\PriceInterface;

/**
 * Calculator calculates charges for given input data
 *
 * @author Andrii Vasyliev <sol@hiqdev.com>
 */
interface CalculatorInterface
{
    /**
     * Calculates Charges for the given $order
     *
     * @param OrderInterface $order
     * @return ChargeInterface[]
     */
    public function calculateOrder(OrderInterface $order): array;

    /**
     * Calculates all Charges for the given $price and $action by running {@see calculateCharge}
     * and applying all possible modifiers that produce more charges.
     *
     * @param PriceInterface $price
     * @param ActionInterface $action
     * @return ChargeInterface[]
     */
    public function calculatePrice(PriceInterface $price, ActionInterface $action): array;

    /**
     * Calculates a single charge out of $price and $action.
     * In difference to {@see calculatePrice}, this method will only create a primary charge,
     * without trying to produce more charges using modifiers.
     *
     * @param PriceInterface $price
     * @param ActionInterface $action
     * @return ChargeInterface|null
     */
    public function calculateCharge(PriceInterface $price, ActionInterface $action): ?ChargeInterface;

    /**
     * Calculates all possible Charges for the given $plan and $action
     *
     * @param PlanInterface $plan
     * @param ActionInterface $action
     * @return ChargeInterface[]
     */
    public function calculatePlan(PlanInterface $plan, ActionInterface $action): array;
}
