<?php
/**
 * PHP Billing Library
 *
 * @link      https://github.com/hiqdev/php-billing
 * @package   php-billing
 * @license   BSD-3-Clause
 * @copyright Copyright (c) 2017-2020, HiQDev (http://hiqdev.com/)
 */

namespace hiqdev\php\billing\tests\unit\order;

use hiqdev\php\billing\action\Action;
use hiqdev\php\billing\charge\Generalizer;
use hiqdev\php\billing\order\Calculator;
use hiqdev\php\billing\order\CalculatorInterface;
use hiqdev\php\billing\order\Order;
use hiqdev\php\billing\order\OrderInterface;
use hiqdev\php\billing\tests\support\plan\SimplePlanRepository;
use hiqdev\php\billing\tests\unit\sale\SaleTest;
use hiqdev\php\billing\tools\CachedDateTimeProvider;
use hiqdev\php\units\Quantity;

class CalculatorTest extends SaleTest
{
    /**
     * @var GeneralizerInterface|Generalizer
     */
    protected $generalizer;
    /**
     * @var CalculatorInterface|Calculator
     */
    protected $calculator;
    /**
     * @var Order|OrderInterface
     */
    protected $order;

    protected function setUp(): void
    {
        parent::setUp();
        $this->generalizer = new Generalizer();
        $planRepository = new SimplePlanRepository();
        $timeProvider = new CachedDateTimeProvider($this->time);
        $this->calculator = new Calculator($this->generalizer, $this->repository, $planRepository, $timeProvider);
        $actions = [];
        foreach ($this->plan->types as $type) {
            foreach ($this->plan->targets as $target) {
                foreach ([1, 2, 3] as $years) {
                    $actions[] = new Action(null, $type, $target, Quantity::year($years), $this->plan->customer, $this->time);
                }
            }
        }
        $this->order = new Order(null, $this->plan->customer, $actions);
    }

    public function testCalculateCharges(): void
    {
        $charges = $this->calculator->calculateOrder($this->order);
        foreach ($this->order->getActions() as $actionKey => $action) {
            $this->checkCharges($action, [$charges[$actionKey]]);
        }
    }
}
