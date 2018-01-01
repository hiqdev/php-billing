<?php
/**
 * PHP Billing Library
 *
 * @link      https://github.com/hiqdev/php-billing
 * @package   php-billing
 * @license   BSD-3-Clause
 * @copyright Copyright (c) 2017-2018, HiQDev (http://hiqdev.com/)
 */

namespace hiqdev\php\billing\tests\unit\plan;

use hiqdev\php\billing\action\Action;
use hiqdev\php\billing\charge\Charge;
use hiqdev\php\units\Quantity;
use hiqdev\php\units\Unit;
use Money\Money;

class PlanTest extends \PHPUnit\Framework\TestCase
{
    protected function setUp()
    {
        $this->plan = CertificatePlan::get();
    }

    public function testCalculateCharges()
    {
        foreach ($this->plan->types as $type) {
            foreach ($this->plan->targets as $target) {
                foreach ([1, 2, 3] as $years) {
                    $usage = Quantity::month($years * 12);
                    $action = new Action(null, $type, $target, $usage);
                    $charges = $this->plan->calculateCharges($action);
                    $this->checkCharges($action, $charges);
                }
            }
        }
    }

    public function checkCharges($action, $charges)
    {
        $this->assertTrue(is_array($charges));
        $this->assertSame(1, count($charges));
        $charge = reset($charges);
        $sum = Money::USD($this->plan->getRawPrice($action));
        $usage = $action->getQuantity()->convert(Unit::year());
        $this->assertInstanceOf(Charge::class, $charge);
        $this->assertSame($action, $charge->getAction());
        $this->assertSame($action->getType(), $charge->getPrice()->getType());
        $this->assertSame($action->getTarget(), $charge->getTarget());
        $this->assertTrue($usage->equals($charge->getUsage()));
        $this->assertTrue($sum->equals($charge->getSum()));
    }
}
