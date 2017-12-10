<?php
/**
 * PHP Billing Library
 *
 * @link      https://github.com/hiqdev/php-billing
 * @package   php-billing
 * @license   BSD-3-Clause
 * @copyright Copyright (c) 2017, HiQDev (http://hiqdev.com/)
 */

namespace hiqdev\php\billing\tests\unit\order;

use hiqdev\php\billing\action\Action;
use hiqdev\php\billing\order\Calculator;
use hiqdev\php\billing\order\Order;
use hiqdev\php\billing\tests\unit\plan\PlanTest;
use hiqdev\php\billing\tests\unit\plan\SimplePlanRepository;
use hiqdev\php\units\Quantity;

class CalculatorTest extends PlanTest
{
    protected function setUp()
    {
        parent::setUp();
        $this->repository = new SimplePlanRepository($this->plan);
        $this->calculator = new Calculator($this->repository);
        $actions = [];
        foreach ($this->plan->types as $type) {
            foreach ($this->plan->targets as $target) {
                foreach ([1, 2, 3] as $years) {
                    $actions[] = new Action(null, $type, $target, Quantity::year($years));
                }
            }
        }
        $this->order = new Order(null, $this->plan->customer, $actions);
    }

    public function testCalculateCharges()
    {
        $charges = $this->calculator->calculateCharges($this->order);
        foreach ($this->order->getActions() as $actionKey => $action) {
            $this->checkCharges($action, $charges[$actionKey]);
        }
    }
}
