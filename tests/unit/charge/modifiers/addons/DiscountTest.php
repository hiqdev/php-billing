<?php
/**
 * PHP Billing Library
 *
 * @link      https://github.com/hiqdev/php-billing
 * @package   php-billing
 * @license   BSD-3-Clause
 * @copyright Copyright (c) 2017-2018, HiQDev (http://hiqdev.com/)
 */

namespace hiqdev\php\billing\tests\unit\charge\modifiers\addons;

use hiqdev\php\billing\charge\modifiers\addons\Discount;
use Money\Money;

/**
 * @author Andrii Vasyliev <sol@hiqdev.com>
 */
class DiscountTest extends \PHPUnit\Framework\TestCase
{
    protected $absolute;
    protected $relative;
    protected $rate = 11;
    protected $sum = 1234;
    protected $currency = 'USD';

    protected function setUp()
    {
        $this->absolute = new Discount($this->sum/100 . ' ' . $this->currency);
        $this->relative = new Discount($this->rate . '%');
    }

    public function testEnsureValidValue()
    {
        $money = Money::USD($this->sum);
        $this->assertEquals($money, $this->absolute->getValue());
        $this->assertEquals($this->rate, $this->relative->getValue());
    }

    public function testMultiply()
    {
        $money = Money::USD($this->sum*10);
        $this->assertEquals($money, $this->absolute->multiply(10)->getValue());
        $this->assertEquals($this->rate*10, $this->relative->multiply(10)->getValue());
    }

    public function badMultipliers()
    {
        return [
            ['aasd'], ['10%'], [Money::USD(12)],
        ];
    }

    /**
     * @dataProvider badMultipliers
     * @expectedException Exception
     */
    public function testMultiplyFailed($multiplier)
    {
        $this->absolute->multiply($multiplier);
    }

    public function testAdd()
    {
        $money = Money::USD($this->sum+10);
        $this->assertEquals($money, $this->absolute->add(Money::USD(10))->getValue());
        $this->assertEquals($this->rate+10, $this->relative->add(10)->getValue());
    }

    public function badAddends()
    {
        $this->setUp();
        return [
            [$this->relative, 'aasd'],
            [$this->relative, '10a'],
            [$this->relative, Money::USD(12)],
            [$this->absolute, 'aasd'],
            [$this->absolute, '10b'],
            [$this->absolute, 10],
        ];
    }

    /**
     * @dataProvider badAddends
     * @expectedException Exception
     */
    public function testAddFailed($discount, $addend)
    {
        $discount->add($addend);
    }

    public function testCompare()
    {
        $money = Money::USD(1);
        $this->assertTrue($this->absolute->compare($money) > 0);
        $this->assertTrue($this->relative->compare(1) > 0);
    }

}
