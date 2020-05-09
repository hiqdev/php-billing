<?php
/**
 * PHP Billing Library
 *
 * @link      https://github.com/hiqdev/php-billing
 * @package   php-billing
 * @license   BSD-3-Clause
 * @copyright Copyright (c) 2017-2020, HiQDev (http://hiqdev.com/)
 */

namespace hiqdev\php\billing\tests\support\order;

use hiqdev\php\billing\charge\Generalizer;
use hiqdev\php\billing\charge\GeneralizerInterface;
use hiqdev\php\billing\order\Calculator;
use hiqdev\php\billing\plan\PlanInterface;
use hiqdev\php\billing\sale\SaleInterface;
use hiqdev\php\billing\tests\support\plan\SimplePlanRepository;
use hiqdev\php\billing\tests\support\sale\SimpleSaleRepository;

class SimpleCalculator extends Calculator
{
    /**
     * @param GeneralizerInterface $generalizer
     * @param SaleRepositoryInterface|SaleInterface $sale
     * @param PlanRepositoryInterface|PlanInterface $plan
     */
    public function __construct(GeneralizerInterface $generalizer = null, $sale = null, $plan = null)
    {
        $this->generalizer = $generalizer ?: new Generalizer();
        if ($sale instanceof SaleInterface) {
            if (empty($plan)) {
                $plan = $sale->getPlan();
            }
            $sale = new SimpleSaleRepository($sale);
        }
        if ($plan instanceof PlanInterface) {
            $plan = new SimplePlanRepository($plan);
        }

        return parent::__construct($this->generalizer, $sale, $plan);
    }

    public function getGeneralizer(): GeneralizerInterface
    {
        return $this->generalizer;
    }
}
