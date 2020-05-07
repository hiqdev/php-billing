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

use hiqdev\php\billing\bill\BillRepositoryInterface;
use hiqdev\php\billing\customer\CustomerInterface;
use hiqdev\php\billing\order\Billing;
use hiqdev\php\billing\order\CalculatorInterface;
use hiqdev\php\billing\plan\PlanInterface;
use hiqdev\php\billing\sale\SaleInterface;
use hiqdev\php\billing\target\Target;
use hiqdev\php\billing\tests\support\bill\SimpleBillRepository;
use hiqdev\php\billing\tools\Aggregator;
use hiqdev\php\billing\tools\AggregatorInterface;
use hiqdev\php\billing\tools\Merger;
use hiqdev\php\billing\tools\MergerInterface;
use hiqdev\php\billing\sale\Sale;

class SimpleBilling extends Billing
{
    public function __construct(
        CalculatorInterface $calculator = null,
        AggregatorInterface $aggregator = null,
        MergerInterface $merger = null,
        $repository = null
    ) {
        $calculator = $calculator ?: new SimpleCalculator();
        $aggregator = $aggregator ?: new Aggregator($calculator->getGeneralizer());
        $merger = $merger ?: new Merger();
        $repository = $repository ?: new SimpleBillRepository();

        parent::__construct($calculator, $aggregator, $merger, $repository, null);
    }

    public function getBillRepository(): BillRepositoryInterface
    {
        return $this->repository;
    }

    public static function fromPlan(PlanInterface $plan, CustomerInterface $customer = null)
    {
        $customer ??= $plan->getSeller();
        $sale = new Sale(null, Target::any(), $customer, $plan);
        return self::fromSale($sale);
    }

    public static function fromSale(SaleInterface $sale)
    {
        return new self(new SimpleCalculator(null, $sale));
    }
}
