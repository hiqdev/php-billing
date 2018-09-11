<?php
/**
 * PHP Billing Library
 *
 * @link      https://github.com/hiqdev/php-billing
 * @package   php-billing
 * @license   BSD-3-Clause
 * @copyright Copyright (c) 2017-2018, HiQDev (http://hiqdev.com/)
 */

namespace hiqdev\php\billing\tests\unit\action;

use DateTimeImmutable;
use hiqdev\php\billing\action\Action;
use hiqdev\php\billing\charge\Charge;
use hiqdev\php\billing\charge\Generalizer;
use hiqdev\php\billing\customer\Customer;
use hiqdev\php\billing\customer\CustomerInterface;
use hiqdev\php\billing\order\Calculator;
use hiqdev\php\billing\plan\Plan;
use hiqdev\php\billing\price\SinglePrice;
use hiqdev\php\billing\sale\Sale;
use hiqdev\php\billing\target\Target;
use hiqdev\php\billing\type\Type;
use hiqdev\php\units\Quantity;
use hiqdev\php\units\QuantityInterface;
use Money\Money;

/**
 * @author Andrii Vasyliev <sol@hiqdev.com>
 */
class ActionTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var SinglePrice
     */
    protected $price;

    /**
     * @var Action
     */
    protected $action;

    /**
     * @var Money
     */
    protected $money;
    /**
     * @var Type
     */
    protected $type;
    /**
     * @var Target
     */
    protected $target;
    /**
     * @var QuantityInterface
     */
    protected $prepaid;
    /**
     * @var Customer|CustomerInterface
     */
    protected $customer;
    /**
     * @var DateTimeImmutable
     */
    protected $time;
    /**
     * @var Generalizer
     */
    protected $generalizer;
    /**
     * @var Calculator
     */
    protected $calculator;

    protected function setUp()
    {
        $this->type     = new Type(null, 'server_traf');
        $this->target   = new Target(2, 'server');
        $this->prepaid  = Quantity::gigabyte(1);
        $this->money    = Money::USD(10000);
        $this->price    = new SinglePrice(5, $this->type, $this->target, null, $this->prepaid, $this->money);
        $this->customer = new Customer(2, 'client');
        $this->time     = new DateTimeImmutable('now');
        $this->generalizer = new Generalizer();
        $this->calculator = new Calculator($this->generalizer, null, null);
    }

    protected function createAction(QuantityInterface $quantity)
    {
        return new Action(null, $this->type, $this->target, $quantity, $this->customer, $this->time);
    }

    protected function tearDown()
    {
    }

    public function testCalculateCharge()
    {
        $action = $this->createAction($this->prepaid->multiply(2));
        $charge = $this->calculator->calculateCharge($this->price, $action);
        $this->assertInstanceOf(Charge::class, $charge);
        $this->assertSame($action, $charge->getAction());
        //$this->assertSame($this->target, $charge->getTarget());
        $this->assertSame($this->type, $charge->getType());
        $this->assertEquals($this->prepaid, $charge->getUsage());
        $this->assertEquals($this->money->multiply($this->prepaid->getQuantity()), $charge->getSum());
    }

    public function testCalculateChargeNull()
    {
        $action = $this->createAction($this->prepaid);
        $charge = $this->calculator->calculateCharge($this->price, $action);
        $this->assertNull($charge);
    }

    public function testChargesForNextMonthSalesAreNotCalculated()
    {
        $action = $this->createAction($this->prepaid->multiply(2));

        $plan = new Plan(null, '', $this->customer, [$this->price]);
        $futureSale = new Sale(null, $this->target, $this->customer, $plan, $this->time->add(new \DateInterval('P1M')));
        $action->setSale($futureSale);

        $charge = $this->calculator->calculateCharge($this->price, $action);
        $this->assertNull($charge);
    }

    public function testChargesForThisMonthSalesAreCalculated()
    {
        $action = $this->createAction($this->prepaid->multiply(2));

        $plan = new Plan(null, '', $this->customer, [$this->price]);
        $futureSale = new Sale(null, $this->target, $this->customer, $plan, $this->time->add(new \DateInterval('PT2S')));
        $action->setSale($futureSale);

        $charge = $this->calculator->calculateCharge($this->price, $action);
        $this->assertNotNull($charge);
    }
}
