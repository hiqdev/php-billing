<?php

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
        $this->target   = new Target('server', 1);
        $this->type     = new Type('server_traf');
        $this->quantity = Quantity::gigabyte(10);
        $this->money    = Money::USD(15);
        $this->price    = new SinglePrice($this->target, $this->type, $this->quantity, $this->money);
    }

    protected function tearDown()
    {
    }

    public function testCalculateUsage()
    {
        $this->assertNull($this->price->calculateUsage(Quantity::byte(1)));
    }
}
