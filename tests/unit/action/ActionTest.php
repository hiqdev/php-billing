<?php
/**
 * PHP Billing Library
 *
 * @link      https://github.com/hiqdev/php-billing
 * @package   php-billing
 * @license   BSD-3-Clause
 * @copyright Copyright (c) 2017-2020, HiQDev (http://hiqdev.com/)
 */

namespace hiqdev\php\billing\tests\unit\action;

use DateTimeImmutable;
use hiqdev\php\billing\action\Action;
use hiqdev\php\billing\charge\Charge;
use hiqdev\php\billing\charge\Generalizer;
use hiqdev\php\billing\customer\Customer;
use hiqdev\php\billing\customer\CustomerInterface;
use hiqdev\php\billing\Exception\CannotReassignException;
use hiqdev\php\billing\order\Calculator;
use hiqdev\php\billing\plan\Plan;
use hiqdev\php\billing\price\SinglePrice;
use hiqdev\php\billing\sale\Sale;
use hiqdev\php\billing\target\Target;
use hiqdev\php\billing\tests\support\plan\SimplePlanRepository;
use hiqdev\php\billing\tests\support\sale\SimpleSaleRepository;
use hiqdev\php\billing\tools\CachedDateTimeProvider;
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

    protected $testId = 12321;

    protected function setUp(): void
    {
        $this->type     = new Type(null, 'server_traf');
        $this->target   = new Target(2, 'server');
        $this->prepaid  = Quantity::gigabyte(1);
        $this->money    = Money::USD(10000);
        $this->price    = new SinglePrice(5, $this->type, $this->target, null, $this->prepaid, $this->money);
        $this->customer = new Customer(2, 'client');
        $this->time     = new DateTimeImmutable('now');
        $this->generalizer = new Generalizer();
        $saleRepository = new SimpleSaleRepository();
        $planRepository = new SimplePlanRepository();
        $timeProvider = new CachedDateTimeProvider($this->time);
        $this->calculator = new Calculator($this->generalizer, $saleRepository, $planRepository, $timeProvider);
    }

    protected function createAction(QuantityInterface $quantity)
    {
        return new Action(null, $this->type, $this->target, $quantity, $this->customer, $this->time);
    }

    protected function tearDown(): void
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

    public function testChargesForThisMonthAreNotCalculatedUntillDateOccurs()
    {
        $createChargeForSaleAt = function (DateTimeImmutable $time): ?Charge {
            $action = $this->createAction($this->prepaid->multiply(2));

            $plan = new Plan(null, '', $this->customer, [$this->price]);
            $futureSale = new Sale(null, $this->target, $this->customer, $plan, $time);
            $action->setSale($futureSale);

            return $this->calculator->calculateCharge($this->price, $action);
        };

        $notOccurredYet = $createChargeForSaleAt(
            // Sale is in future
            $this->time->add(new \DateInterval('PT1M'))
        );
        $this->assertNull($notOccurredYet);

        $occurred = $createChargeForSaleAt(
            // Sale is in past
            $this->time->sub(new \DateInterval('PT1M'))
        );
        $this->assertNotNull($occurred);
    }

    public function testGetHasSetId()
    {
        $this->expectException(CannotReassignException::class);
        $action = $this->createAction($this->prepaid->multiply(2));
        $this->assertFalse($action->hasId());
        $action->setId($this->testId);
        $this->assertTrue($action->hasId());
        $this->assertSame($this->testId, $action->getId());
        $action->setId((string) $this->testId);
        $this->assertSame($this->testId, $action->getId());
        $action->setId((int) $this->testId);
        $this->assertSame($this->testId, $action->getId());
        $action->setId('other id cannot be set');
    }
}
