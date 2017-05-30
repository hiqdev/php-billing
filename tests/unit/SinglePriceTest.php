<?php
/**
 * PHP Billing Library
 *
 * @link      https://github.com/hiqdev/php-billing
 * @package   php-billing
 * @license   BSD-3-Clause
 * @copyright Copyright (c) 2017, HiQDev (http://hiqdev.com/)
 */

namespace hiqdev\php\billing\tests\unit;

use hiqdev\php\billing\SinglePrice;
use hiqdev\php\billing\Target;
use hiqdev\php\billing\Type;
use hiqdev\php\units\Quantity;
use Money\Money;

/**
 * @author Andrii Vasyliev <sol@hiqdev.com>
 */
class SinglePriceTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var SinglePrice
     */
    protected $price;

    /**
     * @var SimpleAction
     */
    protected $action;

    /**
     * @var Money
     */
    protected $money;

    protected function setUp()
    {
        $this->target   = new Target(1, new Type(2, 'server'));
        $this->type     = new Type(3, 'server_traf');
        $this->quantity = Quantity::gigabyte(10);
        $this->money    = Money::USD(15);
        $this->price    = new SinglePrice(5, $this->target, $this->type, $this->quantity, $this->money);
    }

    protected function tearDown()
    {
    }

    public function testCalculateUsage()
    {
        $this->assertNull($this->price->calculateUsage(Quantity::byte(1)));
        $this->assertEquals(Quantity::gigabyte(90), $this->price->calculateUsage(Quantity::gigabyte(100)));
    }
}
