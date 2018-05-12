<?php
/**
 * PHP Billing Library
 *
 * @link      https://github.com/hiqdev/php-billing
 * @package   php-billing
 * @license   BSD-3-Clause
 * @copyright Copyright (c) 2017-2018, HiQDev (http://hiqdev.com/)
 */

namespace hiqdev\php\billing\tests\unit\charge\FixedDiscountTest;

use hiqdev\php\billing\charge\modifiers\Discount;
use hiqdev\php\billing\charge\modifiers\FixedDiscount;
use hiqdev\php\billing\charge\modifiers\GrowingDiscount;

/**
 * @author Andrii Vasyliev <sol@hiqdev.com>
 */
class DiscountTest extends \PHPUnit\Framework\TestCase
{
    protected $discount;

    protected function setUp()
    {
        parent::setUp();
        $this->discount = new Discount();
    }

    public function testFixed()
    {
        $this->assertInstanceOf(FixedDiscount::class, $this->discount->fixed(2));
    }

    public function testGrows()
    {
        $this->assertInstanceOf(GrowingDiscount::class, $this->discount->grows(2));
    }
}
