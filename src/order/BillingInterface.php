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

use hiqdev\php\billing\bill\Bill;

/**
 * Billing calculates and saves bills for given order.
 *
 * @author Andrii Vasyliev <sol@hiqdev.com>
 */
interface BillingInterface
{
    /**
     * @return Bill[]
     */
    public function calculate(OrderInterface $order): array;

    /**
     * @return BillInterface[] array of charges
     */
    public function perform(OrderInterface $order): array;
}
