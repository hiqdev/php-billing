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
        $this->seller   = new Customer(1, 'seller');
        $this->customer = new Customer(2, 'client', $this->seller);
        $this->purchase = new Type('certificate_purchase');
        $this->renewal  = new Type('certificate_renewal');
        $this->rapidssl = new Target('certificate_type', 'rapidssl_standard');
        $this->verisign = new Target('certificate_type', 'verisign_standard');
        $this->money    = Money::USD(15);
        $this->types = [
            'purchase'  => $this->purchase,
            'renewal'   => $this->renewal,
        ];
        $this->targets = [
            'rapidssl'  => $this->rapidssl,
            'verisign'  => $this->verisign,
        ];
        $this->prices   = [
            'purchase_rapidssl' => [
                1 => Money::USD(1129),
                2 => Money::USD(1219),
                3 => Money::USD(1309),
            ],
            'renewal_rapidssl' => [
                1 => Money::USD(1125),
                2 => Money::USD(1215),
                3 => Money::USD(1305),
            ],
            'purchase_verisign' => [
                1 => Money::USD(2129),
                2 => Money::USD(2219),
                3 => Money::USD(2309),
            ],
            'renewal_verisign' => [
                1 => Money::USD(2125),
                2 => Money::USD(2215),
                3 => Money::USD(2305),
            ],
        ];
        $prices = [];
        foreach ($this->types as $typeName => $type) {
            foreach ($this->targets as $targetName => $target) {
                $prices[] = new EnumPrice(null, $type, $target, Unit::year(), $this->getPrices($typeName, $targetName));
            }
        }
        $this->plan = new Plan(null, 'Test Certificate Plan', $this->seller, $prices);
    }

    public function getPrices($typeName, $targetName) {
        return $this->prices[$typeName . '_' . $targetName];
    }

    public function testCalculateCharge()
    {
        foreach ($this->types as $typeName => $type) {
            foreach ($this->targets as $targetName => $target) {
                foreach ([1, 2, 3] as $years) {
                    $price = $this->getPrices($typeName, $targetName)[$years];
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
