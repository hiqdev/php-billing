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

use DateTimeImmutable;

/**
 * Order Collector collects order from given source.
 * Actual source can be different, see Collector.
 * Also, it can be user shopping for example.
 *
 * ```
 * $cart = new Cart();
 * /// fill cart
 * $bills = $this->billing->calculate($cart)
 * ```
 * @author Andrii Vasyliev <sol@hiqdev.com>
 */
interface CollectorInterface
{
    public function collect($source, DateTimeImmutable $time = null): OrderInterface;
}
