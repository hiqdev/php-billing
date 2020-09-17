<?php
/**
 * PHP Billing Library
 *
 * @link      https://github.com/hiqdev/php-billing
 * @package   php-billing
 * @license   BSD-3-Clause
 * @copyright Copyright (c) 2017-2020, HiQDev (http://hiqdev.com/)
 */

namespace hiqdev\php\billing\tests\unit\tools;

use hiqdev\php\billing\action\Action;
use hiqdev\php\billing\charge\Generalizer;
use hiqdev\php\billing\order\Calculator;
use hiqdev\php\billing\order\CalculatorInterface;
use hiqdev\php\billing\order\Order;
use hiqdev\php\billing\order\OrderInterface;
use hiqdev\php\billing\tests\support\plan\SimplePlanRepository;
use hiqdev\php\billing\tests\unit\plan\CertificatePlan;
use hiqdev\php\billing\tests\unit\sale\SaleTest;
use hiqdev\php\billing\tools\Aggregator;
use hiqdev\php\billing\tools\AggregatorInterface;
use hiqdev\php\units\Quantity;
use Money\Money;
use hiqdev\php\billing\tools\CachedDateTimeProvider;

class AggregatorTest extends SaleTest
{
    /**
     * @var Generalizer|GeneralizerInterface
     */
    protected $generalizer;
    /**
     * @var Calculator|CalculatorInterface
     */
    protected $calculator;
    /**
     * @var Aggregator|AggregatorInterface
     */
    protected $aggregator;
    /**
     * @var Order|OrderInterface
     */
    protected $order;
    /**
     * @var CertificatePlan
     */
    protected $plan;

    protected function setUp(): void
    {
        parent::setUp();
        $this->generalizer = new Generalizer();
        $planRepository = new SimplePlanRepository();
        $timeProvider = new CachedDateTimeProvider($this->time);
        $this->calculator = new Calculator($this->generalizer, $this->repository, $planRepository, $timeProvider);
        $this->aggregator = new Aggregator($this->generalizer);
        $actions = [];
        foreach ($this->plan->types as $type) {
            foreach ($this->plan->targets as $target) {
                foreach ([1, 2, 3] as $years) {
                    $actions[] = new Action(null, $type, $target, Quantity::year($years), $this->plan->customer, $this->time);
                }
            }
        }
        shuffle($actions);
        $this->order = new Order(null, $this->plan->customer, $actions);
    }

    public function testCalculateCharges()
    {
        $charges = $this->calculator->calculateOrder($this->order);
        $bills = $this->aggregator->aggregateCharges($charges);
        $this->assertCount(4, $bills);
        foreach ($bills as $bill) {
            $prices = $this->plan->getRawPrices($bill->getType(), $bill->getTarget());
            $sum = Money::USD(array_sum($prices));
            $this->assertTrue($sum->negative()->equals($bill->getSum()));
            $this->assertEquals(6, $bill->getQuantity()->getQuantity());
            $this->assertEquals(3, count($bill->getCharges()));
            foreach ($bill->getCharges() as $charge) {
                $this->assertTrue($bill->getType()->equals($charge->getType()));
                $this->assertTrue($bill->getTarget()->equals($charge->getTarget()));
            }
        }
    }
}
