<?php
/**
 * PHP Billing Library
 *
 * @link      https://github.com/hiqdev/php-billing
 * @package   php-billing
 * @license   BSD-3-Clause
 * @copyright Copyright (c) 2017-2020, HiQDev (http://hiqdev.com/)
 */

namespace hiqdev\php\billing\tests\unit\charge\modifiers\addons;

use hiqdev\php\billing\charge\modifiers\addons\Discount;
use Money\Money;

/**
 * @author Andrii Vasyliev <sol@hiqdev.com>
 */
class DiscountTest extends \PHPUnit\Framework\TestCase
{
    private const int RATE = 11;

    private const int SUM = 1234;

    private Discount $absolute;
    
    private Discount $relative;
    
    protected function setUp(): void
    {
        $this->absolute = self::createAbsoluteDiscount();
        $this->relative = self::createRelativeDiscount();
    }
    
    private static function createAbsoluteDiscount(): Discount
    {
        $currency = 'USD';
        
        return new Discount(self::SUM / 100 . ' ' . $currency);
    }
    
    private static function createRelativeDiscount(): Discount
    {
        return new Discount(self::RATE . '%');
    }

    public function testEnsureValidValue(): void
    {
        $money = Money::USD(self::SUM);
        $this->assertEquals($money, $this->absolute->getValue());
        $this->assertEquals(self::RATE, $this->relative->getValue());
    }

    public function testMultiply(): void
    {
        $money = Money::USD(self::SUM*10);
        $this->assertEquals($money, $this->absolute->multiply(10)->getValue());
        $this->assertEquals(self::RATE*10, $this->relative->multiply(10)->getValue());
    }

    public static function badMultipliers(): iterable
    {
        return [
            ['aasd'], ['10%'], [Money::USD(12)],
        ];
    }

    #[\PHPUnit\Framework\Attributes\DataProvider('badMultipliers')]
    public function testMultiplyFailed($multiplier): void
    {
        $this->expectException(\Exception::class);
        $this->absolute->multiply($multiplier);
    }

    public function testAdd(): void
    {
        $money = Money::USD(self::SUM+10);
        $this->assertEquals($money, $this->absolute->add(Money::USD(10))->getValue());
        $this->assertEquals(self::RATE+10, $this->relative->add(10)->getValue());
    }

    public static function badAddends(): iterable
    {
        $relative = self::createRelativeDiscount();
        $absolute = self::createAbsoluteDiscount();

        return [
            [$relative, 'aasd'],
            [$relative, '10a'],
            [$relative, Money::USD(12)],
            [$absolute, 'aasd'],
            [$absolute, '10b'],
            [$absolute, 10],
        ];
    }

    #[\PHPUnit\Framework\Attributes\DataProvider('badAddends')]
    public function testAddFailed($discount, $addend): void
    {
        $this->expectException(\Exception::class);
        $discount->add($addend);
    }

    public function testCompare(): void
    {
        $money = Money::USD(1);
        $this->assertTrue($this->absolute->compare($money) > 0);
        $this->assertTrue($this->relative->compare(1) > 0);
    }
}
