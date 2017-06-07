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
use hiqdev\php\billing\charge\Charge;
use hiqdev\php\billing\action\SimpleAction;
use hiqdev\php\billing\price\SinglePrice;
use hiqdev\php\billing\target\Target;
use hiqdev\php\billing\type\Type;
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
        $this->type     = new Type('server_traf');
        $this->target   = new Target('server', 2);
        $this->prepaid  = Quantity::gigabyte(10);
        $this->money    = Money::USD(15);
        $this->price    = new SinglePrice(5, $this->type, $this->target, $this->prepaid, $this->money);
    }

    protected function createAction($quantity)
    {
        return new SimpleAction(null, $this->type, $this->target, $quantity);
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
