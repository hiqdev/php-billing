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

use Exception;
use hiqdev\php\billing\action\ActionInterface;
use hiqdev\php\billing\action\TemporaryActionInterface;
use hiqdev\php\billing\charge\Charge;
use hiqdev\php\billing\charge\ChargeInterface;
use hiqdev\php\billing\charge\ChargeModifier;
use hiqdev\php\billing\charge\GeneralizerInterface;
use hiqdev\php\billing\Exception\ActionChargingException;
use hiqdev\php\billing\plan\Plan;
use hiqdev\php\billing\plan\PlanInterface;
use hiqdev\php\billing\plan\PlanRepositoryInterface;
use hiqdev\php\billing\price\PriceInterface;
use hiqdev\php\billing\sale\SaleInterface;
use hiqdev\php\billing\sale\SaleRepositoryInterface;
use hiqdev\php\billing\tools\ActualDateTimeProvider;
use hiqdev\php\billing\tools\CurrentDateTimeProviderInterface;
use Throwable;

/**
 * Calculator calculates charges for given order or action.
 *
 * @author Andrii Vasyliev <sol@hiqdev.com>
 */
class Calculator implements CalculatorInterface
{
    protected GeneralizerInterface $generalizer;
    private SaleRepositoryInterface $saleRepository;
    private PlanRepositoryInterface $planRepository;

    private CurrentDateTimeProviderInterface $dateTimeProvider;

    public function __construct(
        GeneralizerInterface $generalizer,
        SaleRepositoryInterface $saleRepository,
        PlanRepositoryInterface $planRepository,
        CurrentDateTimeProviderInterface $dateTimeProvider = null
    ) {
        $this->generalizer    = $generalizer;
        $this->saleRepository = $saleRepository;
        $this->planRepository = $planRepository;
        $this->dateTimeProvider = $dateTimeProvider ?? new ActualDateTimeProvider();
    }

    /**
     * {@inheritdoc}
     */
    public function calculateOrder(OrderInterface $order): array
    {
        $plans = $this->findPlans($order);
        $charges = [];
        foreach ($order->getActions() as $actionKey => $action) {
            if (!empty($plans[$actionKey])) {
                try {
                    $charges = array_merge($charges, $this->calculatePlan($plans[$actionKey], $action));
                } catch (Throwable $e) {
                    throw ActionChargingException::forAction($action, $e);
                }
            }
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

    /**
     * {@inheritdoc}
     */
    public function calculatePrice(PriceInterface $price, ActionInterface $action): array
    {
        $charge = $this->calculateCharge($price, $action);
        if ($charge === null) {
            return [];
        }

        if ($price instanceof ChargeModifier) {
            $charges = $price->modifyCharge($charge, $action);
        } else {
            $charges = [$charge];
        }

        if ($action->isNotActive()) {
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
     * @return ChargeInterface|Charge|null
     */
    public function calculateCharge(PriceInterface $price, ActionInterface $action): ?ChargeInterface
    {
        if (!$action->isApplicable($price)) {
            return null;
        }

        if ($action->getSale() !== null && $action->getSale()->getTime() > $this->dateTimeProvider->dateTimeImmutable()) {
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
     * @throws Exception
     * @return PlanInterface[]
     */
    private function findPlans(OrderInterface $order): array
    {
        $sales = $this->findSales($order);
        $plans = [];
        $lookPlanIds = [];
        foreach ($order->getActions() as $actionKey => $action) {
            if (!empty($sales[$actionKey])) {
                $sale = $sales[$actionKey];
                /** @var Plan|PlanInterface[] $plan */
                $plan = $sale->getPlan();

                if ($action instanceof TemporaryActionInterface && $plan->getId() && !$action->hasSale()) {
                    $action->setSale($sale);
                }

                if ($plan->hasPrices()) {
                    $plans[$actionKey] = $plan;
                } elseif ($plan->getId() !== null) {
                    $lookPlanIds[$actionKey] = $plan->getId();
                } else {
                    $plans[$actionKey] = null;
                }
            } else {
                // It is ok when no sale found for upper resellers
                $plans[$actionKey] = null;
            }
        }

        if ($lookPlanIds) {
            $foundPlans = $this->planRepository->findByIds($lookPlanIds);
            foreach ($foundPlans as $plan) {
                $foundPlans[$plan->getId()] = $plan;
            }
            foreach ($lookPlanIds as $actionKey => $planId) {
                if (empty($foundPlans[$planId])) {
                    throw new Exception('not found plan');
                }
                $plans[$actionKey] = $foundPlans[$planId];
            }
        }

        return $plans;
    }

    /**
     * @param OrderInterface $order
     * @return SaleInterface[]
     */
    private function findSales(OrderInterface $order): array
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
            foreach ($foundSales as $actionKey => $sale) {
                $sales[$actionKey] = $sale;
                if ($sale !== false) {
                    $lookActions[$actionKey]->setSale($sale);
                }
            }
        }

        return $sales;
    }
}
