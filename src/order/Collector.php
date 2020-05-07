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
use hiqdev\php\billing\action\ActionInterface;

/**
 * Creates order from given source:
 * - Order: just passs by. Can be prepared more in other implementations.
 * - Action or Action[]: create order from given action(s)
 * @author Andrii Vasyliev <sol@hiqdev.com>
 */
class Collector implements CollectorInterface
{
    public function collect($source, DateTimeImmutable $time = null): OrderInterface
    {
        if ($source instanceof OrderInterface) {
            return $source;
        }
        if ($source instanceof ActionInterface) {
            return Order::fromAction($source);
        }
        if (is_array($source)) {
            $item = reset($source);
            if ($item instanceof OrderInterface) {
                return $this->mergeOrders($source);
            }
            if ($item instanceof ActionInterface) {
                return Order::fromActions($source);
            }
        }

        throw new \Exception('unknown order source');
    }

    protected function mergeOrders(array $orders): OrderInterface
    {
        $actions = [];
        foreach ($orders as $order) {
            $actions = array_merge($actions, $order->getActions());
        }

        return Order::fromActions($actions);
    }
}
