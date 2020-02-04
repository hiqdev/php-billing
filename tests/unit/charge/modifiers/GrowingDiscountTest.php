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

use DateTimeImmutable;
use hiqdev\php\billing\charge\Charge;
use hiqdev\php\billing\charge\modifiers\GrowingDiscount;
use hiqdev\php\units\Quantity;

/**
 * @author Andrii Vasyliev <sol@hiqdev.com>
 */
class GrowingDiscountTest extends FixedDiscountTest
{
    protected function buildDiscount($value)
    {
        $month = (new DateTimeImmutable())->modify('first day of this month midnight');

        return (new GrowingDiscount($value))->since($month)->every('month')->till('3000-01');
    }

    public function assertCharges($fd, $sum)
    {
        $action = $this->createAction($this->prepaid->multiply(2));
        $charge = $this->calculator->calculateCharge($this->price, $action);
        $charges = $fd->modifyCharge($charge, $action);
        $this->assertInternalType('array', $charges);
        $this->assertSame(2, count($charges));
        $this->assertSame($charge, $charges[0]);
        $discount = $charges[1];
        $this->assertInstanceOf(Charge::class, $discount);
        $this->assertEquals(Quantity::items(0), $discount->getUsage());
        $this->assertEquals($sum->multiply(-1), $discount->getSum());
    }
}
