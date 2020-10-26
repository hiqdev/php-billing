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

use DateTimeImmutable;
use hiqdev\php\billing\action\ActionInterface;

/**
 * Order Collector collects the order from given source.
 * Actual source can be anything, that {@see Collector} can handle.
 *
 * For example, it can be a shopping cart:
 *
 * ```php
 * $cart = new Cart();
 * /// fill the cart
 * $order = $this->billing->calculate($cart);
 * ```
 *
 * @author Andrii Vasyliev <sol@hiqdev.com>
 */
interface CollectorInterface
{
    /**
     * @param OrderInterface|ActionInterface|mixed $source
     * @param DateTimeImmutable|null $time
     * @return OrderInterface
     */
    public function collect($source, DateTimeImmutable $time = null): OrderInterface;
}
