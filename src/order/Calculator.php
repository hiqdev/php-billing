<?php
/**
 * PHP Billing Library
 *
 * @link      https://github.com/hiqdev/php-billing
 * @package   php-billing
 * @license   BSD-3-Clause
 * @copyright Copyright (c) 2017, HiQDev (http://hiqdev.com/)
 */

namespace hiqdev\php\billing\order;

use hiqdev\php\billing\plan\PlanRepositoryInterface;

/**
 * @author Andrii Vasyliev <sol@hiqdev.com>
 */
class Calculator implements CalculatorInterface
{
    public $repository;

    public function __construct(PlanRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    public function calculateCharges(OrderInterface $order)
    {
        $plans = $this->repository->findByOrder($order);
        $charges = [];
        foreach ($order->getActions() as $actionKey => $action) {
            if (empty($plans[$actionKey])) {
                throw new FailedFindPlan();
            }
            $charges[$actionKey] = $plans[$actionKey]->calculateCharges($action);
        }

        return $charges;
    }
}
