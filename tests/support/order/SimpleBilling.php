<?php
/**
 * PHP Billing Library
 *
 * @link      https://github.com/hiqdev/php-billing
 * @package   php-billing
 * @license   BSD-3-Clause
 * @copyright Copyright (c) 2017-2018, HiQDev (http://hiqdev.com/)
 */

namespace hiqdev\php\billing\tests\support\order;

use hiqdev\php\billing\charge\Generalizer;
use hiqdev\php\billing\order\Billing;
use hiqdev\php\billing\order\BillingInterface;
use hiqdev\php\billing\order\Calculator;
use hiqdev\php\billing\order\OrderInterface;
use hiqdev\php\billing\plan\PlanInterface;
use hiqdev\php\billing\sale\SaleInterface;
use hiqdev\php\billing\tests\support\plan\SimplePlanRepository;
use hiqdev\php\billing\tests\support\sale\SimpleSaleRepository;
use hiqdev\php\billing\tools\Aggregator;
use hiqdev\php\billing\tools\Merger;

class SimpleBilling implements BillingInterface
{
    private $billing;

    public function __construct(SaleInterface $sale = null, PlanInterface $plan = null)
    {
        $saleRepo = $sale ? new SimpleSaleRepository($sale) : null;
        $planRepo = $plan ? new SimplePlanRepository($plan) : null;
        $calculator = new Calculator(new Generalizer(), $saleRepo, $planRepo);
        $aggregator = new Aggregator(new Generalizer());
        $this->billing = new Billing($calculator, $aggregator, new Merger(), null);
    }

    public function calculate(OrderInterface $order): array
    {
        return $this->billing->calculate($order);
    }


    public function perform(OrderInterface $order): array
    {
        return $this->billing->perform($order);
    }
}
