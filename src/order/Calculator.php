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

use hiqdev\php\billing\action\ActionInterface;
use hiqdev\php\billing\charge\Charge;
use hiqdev\php\billing\charge\ChargeInterface;
use hiqdev\php\billing\charge\ChargeModifier;
use hiqdev\php\billing\charge\GeneralizerInterface;
use hiqdev\php\billing\plan\Plan;
use hiqdev\php\billing\plan\PlanInterface;
use hiqdev\php\billing\plan\PlanRepositoryInterface;
use hiqdev\php\billing\price\PriceInterface;
use hiqdev\php\billing\sale\Sale;
use hiqdev\php\billing\sale\SaleInterface;
use hiqdev\php\billing\sale\SaleRepositoryInterface;
use hiqdev\php\billing\type\TypeInterface;

/**
 * Calculator calculates charges for given order or action.
 *
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
        GeneralizerInterface $generalizer,
        ?SaleRepositoryInterface $saleRepository,
        ?PlanRepositoryInterface $planRepository
    ) {
        $this->generalizer    = $generalizer;
        $this->saleRepository = $saleRepository;
        $this->planRepository = $planRepository;
    }

    /**
     * {@inheritdoc}
     */
    public function calculateOrder(OrderInterface $order): array
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

            $charges[$actionKey] = $this->calculatePlan($plans[$actionKey], $action);
        }

        return $charges;
    }

    public function calculatePlan(PlanInterface $plan, ActionInterface $action): array
    {
        $result = [];
        foreach ($plan->getPrices() as $price) {
            $charges = $this->calculatePrice($price, $action);
            if (!empty($charges)) {
                $result = array_merge($result, $charges);
            }
        }

        return $result;
    }

    public function calculatePrice(PriceInterface $price, ActionInterface $action): array
    {
        $charge = $this->calculateCharge($price, $action);
        if ($charge === null) {
            return [];
        }

        $charges = [$charge];
        if ($price instanceof ChargeModifier) {
            $charges = $price->modifyCharge($charge, $action);
        }

        if ($action->isFinished()) {
            foreach ($charges as $charge) {
                $charge->setFinished();
            }
        }

        return $charges;
    }

    /**
     * Calculates charge for given action and price.
     * Returns `null`, if $price is not applicable to $action.
     *
     * @param PriceInterface $price
     * @param ActionInterface $action
     * @return ChargeInterface|Charge|null
     */
    public function calculateCharge(PriceInterface $price, ActionInterface $action): ?ChargeInterface
    {
        if (!$action->isApplicable($price)) {
            return null;
        }

        $usage = $price->calculateUsage($action->getQuantity());
        if ($usage === null) {
            return null;
        }

        $sum = $price->calculateSum($action->getQuantity());
        if ($sum === null) {
            return null;
        }

        $type = $this->generalizer->specializeType($price->getType(), $action->getType());
        $target = $this->generalizer->specializeTarget($price->getTarget(), $action->getTarget());

        /* sorry, debugging facility
         * var_dump([
            'unit'      => $usage->getUnit()->getName(),
            'quantity'  => $usage->getQuantity(),
            'price'     => $price->calculatePrice($usage)->getAmount(),
            'sum'       => $sum->getAmount(),
        ]);*/

        return new Charge(null, $type, $target, $action, $price, $usage, $sum);
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
