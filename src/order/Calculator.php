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

use hiqdev\php\billing\plan\Plan;
use hiqdev\php\billing\plan\PlanInterface;
use hiqdev\php\billing\plan\PlanRepositoryInterface;
use hiqdev\php\billing\sale\Sale;
use hiqdev\php\billing\sale\SaleInterface;
use hiqdev\php\billing\sale\SaleRepositoryInterface;

/**
 * @author Andrii Vasyliev <sol@hiqdev.com>
 */
class Calculator implements CalculatorInterface
{
    /**
     * @var SaleRepositoryInterface
     */
    private $saleRepository;

    /**
     * @var PlanRepositoryInterface
     */
    private $planRepository;

    /**
     * @param SaleRepositoryInterface|null $saleRepository
     * @param PlanRepositoryInterface $planRepository
     * @throws \Exception
     */
    public function __construct(
        ?SaleRepositoryInterface $saleRepository,
        ?PlanRepositoryInterface $planRepository
    ) {
        $this->saleRepository = $saleRepository;
        $this->planRepository = $planRepository;
    }

    /**
     * {@inheritdoc}
     */
    public function calculateCharges(OrderInterface $order)
    {
        $plans = $this->findPlans($order);
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

    /**
     * @param OrderInterface $order
     * @return PlanInterface[]|Plan
     * @throws \Exception
     */
    public function findPlans(OrderInterface $order)
    {
        $sales = $this->findSales($order);
        $plans = [];
        $lookPlanIds = [];
        foreach ($order->getActions() as $actionKey => $action) {
            if (empty($sales[$actionKey])) {
                /// it is ok when no sale found for upper resellers
                /// throw new \Exception('not found sale');
                $plans[$actionKey] = null;
            } else {
                $sale = $sales[$actionKey];
                /** @var Plan|PlanInterface[] $plan */
                $plan = $sale->getPlan();
                if ($plan->hasPrices()) {
                    $plans[$actionKey] = $plan;
                } else {
                    $lookPlanIds[$actionKey] = $plan->getId();
                }
            }
        }

        if ($lookPlanIds) {
            $foundPlans = $this->planRepository->findByIds($lookPlanIds);
            foreach ($foundPlans as $actionKey => $plan) {
                $foundPlans[$plan->getId()] = $plan;
            }
            foreach ($lookPlanIds as $actionKey => $planId) {
                if (empty($foundPlans[$planId])) {
                    throw new \Exception('not found plan');
                }
                $plans[$actionKey] = $foundPlans[$planId];
            }
        }

        return $plans;
    }

    /**
     * @param OrderInterface $order
     * @return SaleInterface[]|Sale
     */
    public function findSales(OrderInterface $order)
    {
        $sales = [];
        $lookActions = [];
        foreach ($order->getActions() as $actionKey => $action) {
            $sale = $action->getSale();
            if ($sale) {
                $sales[$actionKey] = $sale;
            } else {
                $lookActions[$actionKey] = $action;
            }
        }

        if ($lookActions) {
            $lookOrder = new Order(null, $order->getCustomer(), $lookActions);
            $foundSales = $this->saleRepository->findByOrder($lookOrder);
            foreach ($foundSales as $actionKey => $plan) {
                $sales[$actionKey] = $plan;
            }
        }

        return $sales;
    }
}
