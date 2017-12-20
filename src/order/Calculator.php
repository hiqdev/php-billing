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

use hiqdev\billing\hiapi\plan\PlanRepository;
use hiqdev\php\billing\plan\PlanRepositoryInterface;

/**
 * @author Andrii Vasyliev <sol@hiqdev.com>
 */
class Calculator implements CalculatorInterface
{
    /**
     * @var PlanRepositoryInterface|PlanRepository
     */
    private $planRepository;

    /**
     * @param PlanRepositoryInterface $planRepository
     */
    public function __construct(PlanRepositoryInterface $planRepository)
    {
        $this->planRepository = $planRepository;
    }

    /**
     * {@inheritdoc}
     */
    public function calculateCharges(OrderInterface $order)
    {
        $plans = $this->planRepository->findByOrder($order);
        $charges = [];
        foreach ($order->getActions() as $actionKey => $action) {
            if (empty($plans[$actionKey])) {
                /* XXX not sure... think more
                throw new FailedFindPlan();
                 */
                continue;
            }
            $charges[$actionKey] = $plans[$actionKey]->calculateCharges($action);
        }

        return $charges;
    }
}
