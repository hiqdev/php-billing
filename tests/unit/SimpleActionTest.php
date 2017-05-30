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

use DateTime;
use hiqdev\php\billing\Charge;
use hiqdev\php\billing\Customer;
use hiqdev\php\billing\SimpleAction;
use hiqdev\php\billing\SinglePrice;
use hiqdev\php\billing\Target;
use hiqdev\php\billing\Type;
use hiqdev\php\units\Quantity;
use Money\Money;

/**
 * @author Andrii Vasyliev <sol@hiqdev.com>
 */
class SimpleActionTest extends \PHPUnit\Framework\TestCase
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
        $this->customer = new Customer(1, 'test', 'seller');
        $this->target   = new Target(2, new Type(3, 'server'));
        $this->type     = new Type(4, 'server_traf');
        $this->prepaid  = Quantity::gigabyte(10);
        $this->money    = Money::USD(15);
        $this->now      = new DateTime('now');
        $this->price    = new SinglePrice(5, $this->target, $this->type, $this->prepaid, $this->money);
    }

    protected function createAction($quantity)
    {
        return new SimpleAction($this->customer, $this->target, $quantity, $this->now, $this->type);
    }

    protected function tearDown()
    {
    }

    public function testCalculateCharge()
    {
        $action = $this->createAction($this->prepaid->multiply(2));
        $charge = $action->calculateCharge($this->price);
        $this->assertInstanceOf(Charge::class, $charge);
        $this->assertSame($action, $charge->getAction());
        $this->assertSame($this->target, $charge->getTarget());
        $this->assertSame($this->type, $charge->getType());
        $this->assertEquals($this->prepaid, $charge->getUsage());
        $this->assertEquals($this->money->multiply($this->prepaid->getQuantity()), $charge->getSum());
    }

    public function testCalculateChargeNull()
    {
        $action = $this->createAction($this->prepaid);
        $charge = $action->calculateCharge($this->price);
        $this->assertNull($charge);
    }
}
