<?php
/**
 * PHP Billing Library
 *
 * @link      https://github.com/hiqdev/php-billing
 * @package   php-billing
 * @license   BSD-3-Clause
 * @copyright Copyright (c) 2017, HiQDev (http://hiqdev.com/)
 */

namespace hiqdev\php\billing\tests\unit\plan;

use hiqdev\php\billing\charge\Charge;
use hiqdev\php\billing\customer\Customer;
use hiqdev\php\billing\action\SimpleAction;
use hiqdev\php\billing\plan\Plan;
use hiqdev\php\billing\price\EnumPrice;
use hiqdev\php\billing\target\Target;
use hiqdev\php\billing\type\Type;
use hiqdev\php\units\Quantity;
use hiqdev\php\units\Unit;
use Money\Money;

class PlanTest extends \PHPUnit\Framework\TestCase
{
    protected function setUp()
    {
        $this->plan = CertificatePlan::get();
    }

    public function testCalculateCharge()
    {
        foreach ($this->plan->types as $typeName => $type) {
            foreach ($this->plan->targets as $targetName => $target) {
                foreach ([1, 2, 3] as $years) {
                    $price = $this->plan->getRawPrices($typeName, $targetName)[$years];
                    $this->checkCharge($type, $target, $years, $price);
                }
            }
        }
    }

    public function checkCharge($type, $target, $years, $sum)
    {
        $usage = Quantity::month($years*12);
        $action = new SimpleAction(null, $type, $target, $usage);
        $charges = $this->plan->calculateCharges($action);
        $this->assertTrue(is_array($charges));
        $this->assertSame(1, count($charges));
        $charge = reset($charges);
        $this->assertInstanceOf(Charge::class, $charge);
        $this->assertSame($action, $charge->getAction());
        $this->assertSame($type, $charge->getType());
        $this->assertSame($target, $charge->getTarget());
        $this->assertTrue($sum->equals($charge->getSum()));
        $this->assertTrue($usage->equals($charge->getUsage()));
    }
}
