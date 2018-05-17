<?php
/**
 * PHP Billing Library
 *
 * @link      https://github.com/hiqdev/php-billing
 * @package   php-billing
 * @license   BSD-3-Clause
 * @copyright Copyright (c) 2017-2018, HiQDev (http://hiqdev.com/)
 */

namespace hiqdev\php\billing\tests\unit\charge\modifiers;

use hiqdev\php\billing\charge\Charge;
use hiqdev\php\billing\charge\modifiers\FixedDiscount;
use hiqdev\php\billing\tests\unit\action\ActionTest;
use hiqdev\php\units\Quantity;
use Money\Currency;
use Money\Money;

/**
 * @author Andrii Vasyliev <sol@hiqdev.com>
 */
class FixedDiscountTest extends ActionTest
{
    protected function setUp()
    {
        parent::setUp();
        $this->value = Money::USD(1000);
        $this->rate = '10';
    }

    public function testCreateAbsolute()
    {
        $abs = new FixedDiscount($this->value);
        $this->assertAbsolute($this->value, $abs);
        $abs = new FixedDiscount('10 USD');
        $this->assertAbsolute($this->value, $abs);
        $abs = new FixedDiscount('10.00 USD');
        $this->assertAbsolute($this->value, $abs);
    }

    public function assertAbsolute($value, $abs)
    {
        $this->assertEquals($value, $abs->getValue());
        $this->assertTrue($abs->isAbsolute());
        $this->assertFalse($abs->isRelative());
    }

    public function testCreateRelative()
    {
        $rel = new FixedDiscount($this->rate);
        $this->assertRelative($this->rate, $rel);
        $rel = new FixedDiscount($this->rate . '%');
        $this->assertRelative($this->rate, $rel);
    }

    protected function assertRelative($rate, $rel)
    {
        $this->assertSame($rate, $rel->getValue());
        $this->assertTrue($rel->isRelative());
        $this->assertFalse($rel->isAbsolute());
    }

    public function testAbsoluteModifyCharge()
    {
        $abs = new FixedDiscount($this->value);
        $this->assertFixedDiscountCharges($abs, $this->value);
    }

    public function testRelativeModifyCharge()
    {
        $rel = new FixedDiscount($this->rate);
        $this->assertFixedDiscountCharges($rel, $this->value);
    }

    public function assertFixedDiscountCharges($fd, $sum)
    {
        $this->price->setFormula($fd);
        $action = $this->createAction($this->prepaid->multiply(2));
        $charge = $action->calculateCharge($this->price);
        $charges = $this->price->calculateCharges($action, $this->context);
        $this->assertInternalType('array', $charges);
        $this->assertSame(2, count($charges));
        $this->assertEquals($charge, $charges[0]);
        $discount = $charges[1];
        $this->assertInstanceOf(Charge::class, $discount);
        $this->assertEquals(Quantity::items(1), $discount->getUsage());
        $this->assertEquals($sum, $discount->getSum());
    }
}
