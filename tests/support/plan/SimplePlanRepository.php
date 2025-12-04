<?php
/**
 * PHP Billing Library
 *
 * @link      https://github.com/hiqdev/php-billing
 * @package   php-billing
 * @license   BSD-3-Clause
 * @copyright Copyright (c) 2017-2020, HiQDev (http://hiqdev.com/)
 */

namespace hiqdev\php\billing\tests\support\plan;

use hiqdev\DataMapper\Query\Specification;
use hiqdev\php\billing\action\ActionInterface;
use hiqdev\php\billing\order\OrderInterface;
use hiqdev\php\billing\plan\PlanInterface;
use hiqdev\php\billing\plan\PlanRepositoryInterface;

class SimplePlanRepository implements PlanRepositoryInterface
{
    protected $plan;

    public function __construct(?PlanInterface $plan = null)
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

    /**
     * {@inheritdoc}
     */
    public function getById(int $id): PlanInterface
    {
        throw new \Exception('not implemented');
    }

    public function count(Specification $specification)
    {
        throw new \Exception('not implemented');
    }

    public function findAll(Specification $specification)
    {
        throw new \Exception('not implemented');
    }

    public function findOne(Specification $specification)
    {
        throw new \Exception('not implemented');
    }

    public function findOneOrFail(Specification $specification)
    {
        throw new \Exception('not implemented');
    }
}
