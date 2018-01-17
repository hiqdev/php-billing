<?php
/**
 * PHP Billing Library
 *
 * @link      https://github.com/hiqdev/php-billing
 * @package   php-billing
 * @license   BSD-3-Clause
 * @copyright Copyright (c) 2017-2018, HiQDev (http://hiqdev.com/)
 */

namespace hiqdev\php\billing\tests\unit\plan;

use hiqdev\php\billing\action\ActionInterface;
use hiqdev\php\billing\order\OrderInterface;
use hiqdev\php\billing\plan\PlanInterface;
use hiqdev\php\billing\plan\PlanRepositoryInterface;

class SimplePlanRepository implements PlanRepositoryInterface
{
    protected $plan;

    public function __construct(PlanInterface $plan)
    {
        $this->plan = $plan;
    }

    public function findByAction(ActionInterface $action)
    {
        return $this->plan;
    }

    public function findByOrder(OrderInterface $order)
    {
        $plans = [];
        foreach (array_keys($order->getActions()) as $actionKey) {
            $plans[$actionKey] = $this->plan;
        }

        return $plans;
    }

    public function findByIds(array $ids)
    {
        throw new \Exception('not implemented');
    }
}
