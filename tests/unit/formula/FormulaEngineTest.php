<?php
/**
 * PHP Billing Library
 *
 * @link      https://github.com/hiqdev/php-billing
 * @package   php-billing
 * @license   BSD-3-Clause
 * @copyright Copyright (c) 2017-2018, HiQDev (http://hiqdev.com/)
 */

namespace hiqdev\php\billing\tests\unit\formula;

use DateTimeImmutable;
use hiqdev\php\billing\formula\FormulaEngine;
use hiqdev\php\billing\charge\modifiers\FixedDiscount;
use hiqdev\php\billing\charge\modifiers\addons\Reason;
use hiqdev\php\billing\charge\modifiers\addons\Since;
use Money\Money;

/**
 * @author Andrii Vasyliev <sol@hiqdev.com>
 */
class FormulaEngineTest extends \PHPUnit\Framework\TestCase
{
    public function setUp()
    {
        $this->engine = new FormulaEngine();
    }

    public function testSimpleDiscount()
    {
        $date = '2018-08-01';
        $rate = '2';
        $reason = 'test reason';
        $formula = $this->engine->build("discount.fixed('$rate%').since('$date').reason('$reason')");

        $this->assertInstanceOf(FixedDiscount::class, $formula);
        $this->assertSame($rate, $formula->getValue());
        $this->assertTrue($formula->isRelative());
        $this->assertInstanceOf(Since::class, $formula->getSince());
        $this->assertEquals(new DateTimeImmutable($date), $formula->getSince()->getValue());
        $this->assertInstanceOf(Reason::class, $formula->getReason());
        $this->assertSame($reason, $formula->getReason()->getValue());
        $this->assertNull($formula->getTill());
    }
}
