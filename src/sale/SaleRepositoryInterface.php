<?php
/**
 * PHP Billing Library
 *
 * @link      https://github.com/hiqdev/php-billing
 * @package   php-billing
 * @license   BSD-3-Clause
 * @copyright Copyright (c) 2017-2018, HiQDev (http://hiqdev.com/)
 */

namespace hiqdev\php\billing\sale;

use hiqdev\php\billing\action\ActionInterface;
use hiqdev\php\billing\order\OrderInterface;
use hiqdev\php\billing\plan\PlanInterface;

/**
 * @author Andrii Vasyliev <sol@hiqdev.com>
 */
interface SaleRepositoryInterface
{
    /**
     * Finds suitable sales for given order.
     * @param OrderInterface $order
     * @return PlanInterface[] array: actionKey => plan
     */
    public function findByOrder(OrderInterface $order);
}
