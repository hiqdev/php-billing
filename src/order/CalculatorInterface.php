<?php
/**
 * PHP Billing Library
 *
 * @link      https://github.com/hiqdev/php-billing
 * @package   php-billing
 * @license   BSD-3-Clause
 * @copyright Copyright (c) 2017-2018, HiQDev (http://hiqdev.com/)
 */

namespace hiqdev\php\billing\order;

use hiqdev\php\billing\action\ActionInterface;
use hiqdev\php\billing\charge\Charge;
use hiqdev\php\billing\charge\ChargeInterface;
use hiqdev\php\billing\price\PriceInterface;

/**
 * Calculator calculates charges for given order or action.
 * @author Andrii Vasyliev <sol@hiqdev.com>
 */
interface CalculatorInterface
{
    /**
     * @param OrderInterface $order
     * @return Charge[]
     */
    public function calculateOrder(OrderInterface $order): array;

    /**
     * @param PriceInterface $price
     * @param ActionInterface $action
     * @return ChargeInterface[] array of charges
     */
    public function calculatePrice(PriceInterface $price, ActionInterface $action): array;
}
