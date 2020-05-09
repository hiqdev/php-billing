<?php
/**
 * PHP Billing Library
 *
 * @link      https://github.com/hiqdev/php-billing
 * @package   php-billing
 * @license   BSD-3-Clause
 * @copyright Copyright (c) 2017-2018, HiQDev (http://hiqdev.com/)
 */

namespace hiqdev\php\billing\tests\unit\tools;

use DateTimeImmutable;
use hiqdev\php\billing\action\Action;
use hiqdev\php\billing\bill\Bill;
use hiqdev\php\billing\charge\Charge;
use hiqdev\php\billing\customer\Customer;
use hiqdev\php\billing\plan\Plan;
use hiqdev\php\billing\price\PriceInterface;
use hiqdev\php\billing\sale\Sale;
use hiqdev\php\billing\target\Target;
use hiqdev\php\billing\tests\support\tools\SimpleFactory;
use hiqdev\php\billing\type\Type;
use hiqdev\php\units\Unit;

class FactoryTest extends \PHPUnit\Framework\TestCase
{
    private $type = 'type';
    private $typeId = 'type-id';

    private $name = 'name';

    private $time = '2020-02-01T00:00:00+00:00';

    private $quantity = '10';
    private $unit = 'items';

    private $sum = '11.99';
    private $currency = 'USD';

    private $user = 'user';
    private $reseller = 'reseller';

    private $planId = 'plan-id';
    private $plan = 'plan';

    private $saleId = 'sale-id';

    private $chargeId = 'charge-id';

    private $billId = 'bill-id';

    private $actionId = 'action-id';

    private $priceId = 'price-id';

    private $targetId = 'target-id';
    private $target = 'type:name';

    protected function setUp()
    {
        $this->factory = new SimpleFactory();
    }

    public function testGetCustomer()
    {
        $c1 = $this->factory->get('customer', ['login' => $this->user, 'seller' => $this->reseller]);
        $c2 = $this->factory->get('customer', ['login' => $this->user, 'seller' => $this->reseller]);
        $c3 = $this->factory->get('customer', ['login' => $this->user]);
        $c4 = $this->factory->get('customer', $this->user);
        $c5 = $this->factory->find('customer', [$this->user]);
        $this->assertInstanceOf(Customer::class, $c1);
        $this->assertSame($this->user, $c1->getLogin());
        $this->assertSame($this->reseller, $c1->getSeller()->getLogin());
        $this->assertSame($c1, $c2);
        $this->assertSame($c1, $c3);
        $this->assertSame($c1, $c4);
        $this->assertSame($c1, $c5);
    }

    public function testGetType()
    {
        $t1 = $this->factory->get('type', ['id' => $this->typeId, 'name' => $this->type]);
        $t2 = $this->factory->get('type', ['id' => $this->typeId, 'name' => $this->type]);
        $t3 = $this->factory->get('type', ['id' => $this->typeId]);
        $t4 = $this->factory->get('type', $this->type);
        $t5 = $this->factory->find('type', [$this->type]);
        $this->assertInstanceOf(Type::class, $t1);
        $this->assertSame($this->type, $t1->getName());
        $this->assertSame($this->typeId, $t1->getId());
        $this->assertSame($t1, $t2);
        $this->assertSame($t1, $t3);
        $this->assertSame($t1, $t4);
        $this->assertSame($t1, $t5);
    }

    public function testGetTarget()
    {
        $t1 = $this->factory->get('target', ['id' => $this->targetId, 'name' => $this->name, 'type' => $this->type]);
        $t2 = $this->factory->get('target', ['name' => $this->name, 'type' => $this->type]);
        $t3 = $this->factory->get('target', ['id' => $this->targetId]);
        $t4 = $this->factory->get('target', $this->targetId);
        $t5 = $this->factory->get('target', $this->type . ':' . $this->name);
        $this->assertInstanceOf(Target::class, $t1);
        $this->assertSame($this->name, $t1->getName());
        $this->assertSame($this->type, $t1->getType());
        $this->assertSame($this->targetId, $t1->getId());
        $this->assertSame($t1, $t2);
        $this->assertSame($t1, $t3);
        $this->assertSame($t1, $t4);
        $this->assertSame($t1, $t5);
    }

    public function testParseTarget()
    {
        $id = $this->type . ':' . $this->name;
        $t1 = $this->factory->get('target', $id);
        $this->assertInstanceOf(Target::class, $t1);
        $this->assertSame($this->name, $t1->getName());
        $this->assertSame($this->type, $t1->getType());
        $this->assertSame($id, $t1->getId());
    }

    public function testGetPlan()
    {
        $str = $this->plan . ' ' . $this->reseller;
        $p1 = $this->factory->get('plan', ['id' => $this->planId, 'name' => $this->plan, 'seller' => $this->reseller]);
        $p2 = $this->factory->get('plan', ['name' => $this->plan, 'seller' => $this->reseller]);
        $p3 = $this->factory->get('plan', ['id' => $this->planId]);
        $p4 = $this->factory->get('plan', $str);
        $p5 = $this->factory->find('plan', [$str]);
        $this->assertInstanceOf(Plan::class, $p1);
        $this->assertSame($this->plan, $p1->getName());
        $this->assertSame($this->reseller, $p1->getSeller()->getLogin());
        $this->assertSame($p1, $p2);
        $this->assertSame($p1, $p3);
        $this->assertSame($p1, $p4);
        $this->assertSame($p1, $p5);
    }

    public function testGetSale()
    {
        $this->testGetCustomer();
        $this->testGetPlan();
        $s1 = $this->factory->get('sale', [
            'id' => $this->saleId,
            'customer' => $this->user,
            'target' => $this->targetId,
            'plan' => $this->planId,
            'time' => $this->time,
        ]);
        $s2 = $this->factory->get('sale', ['id' => $this->saleId, 'target' => $this->targetId]);
        $s3 = $this->factory->get('sale', ['id' => $this->saleId]);
        $s4 = $this->factory->get('sale', $this->saleId);
        $s5 = $this->factory->find('sale', [$this->saleId]);
        $this->assertInstanceOf(Sale::class, $s1);
        $this->assertSame($this->saleId, $s1->getId());
        $this->assertSame($this->user, $s1->getCustomer()->getLogin());
        $this->assertSame($this->time, $s1->getTime()->format('c'));
        $this->assertSame($this->targetId, $s1->getTarget()->getId());
        $this->assertSame($this->planId, $s1->getPlan()->getId());
        $this->assertSame($s1, $s2);
        $this->assertSame($s1, $s3);
        $this->assertSame($s1, $s4);
        $this->assertSame($s1, $s5);
    }

    public function testGetCharge()
    {
        $this->testGetBill();
        $this->testGetPrice();
        $this->testGetAction();
        $charge = $this->factory->get('charge', [
            'id' => $this->chargeId,
            'type' => $this->type,
            'target' => $this->target,
            'action' => $this->actionId,
            'price' => $this->priceId,
            'usage' => $this->quantity . ' ' . $this->unit,
            'sum' => $this->sum . ' ' . $this->currency,
            'bill' => $this->billId,
        ]);
        $e2 = $this->factory->get('charge', ['id' => $this->chargeId, 'target' => $this->targetId]);
        $e3 = $this->factory->get('charge', ['id' => $this->chargeId]);
        $e4 = $this->factory->get('charge', $this->chargeId);
        $e5 = $this->factory->find('charge', [$this->chargeId]);
        $this->assertInstanceOf(Charge::class, $charge);
        $this->assertSame($this->chargeId, $charge->getId());
        $this->assertSame($this->type, $charge->getType()->getName());
        $this->assertSame($this->target, $charge->getTarget()->getFullName());
        $this->assertSame($this->actionId, $charge->getAction()->getId());
        $this->assertSame($this->priceId, $charge->getPrice()->getId());
        $this->assertSame($this->quantity, $charge->getUsage()->getQuantity());
        $this->assertSame($this->unit, $charge->getUsage()->getUnit()->getName());
        $this->assertEquals($this->sum*100, $charge->getSum()->getAmount());
        $this->assertSame($this->currency, $charge->getSum()->getCurrency()->getCode());
        $this->assertSame($this->billId, $charge->getBill()->getId());
        $this->assertSame($charge, $e2);
        $this->assertSame($charge, $e3);
        $this->assertSame($charge, $e4);
        $this->assertSame($charge, $e5);
    }

    public function testGetBill()
    {
        $this->testGetSale();
        $s1 = $this->factory->get('bill', [
            'id' => $this->billId,
            'type' => $this->type,
            'customer' => $this->user,
            'target' => $this->targetId,
            'sum' => $this->sum . ' ' . $this->currency,
            'quantity' => $this->quantity . ' ' . $this->unit,
            'plan' => $this->planId,
            'time' => $this->time,
        ]);
        $s2 = $this->factory->get('bill', ['id' => $this->billId, 'target' => $this->targetId]);
        $s3 = $this->factory->get('bill', ['id' => $this->billId]);
        $s4 = $this->factory->get('bill', $this->billId);
        $s5 = $this->factory->find('bill', [$this->billId]);
        $this->assertInstanceOf(Bill::class, $s1);
        $this->assertSame($this->billId, $s1->getId());
        $this->assertSame($this->user, $s1->getCustomer()->getLogin());
        $this->assertSame($this->time, $s1->getTime()->format('c'));
        $this->assertSame($this->targetId, $s1->getTarget()->getId());
        $this->assertSame($this->planId, $s1->getPlan()->getId());
        $this->assertSame($this->quantity, $s1->getQuantity()->getQuantity());
        $this->assertSame($this->unit, $s1->getQuantity()->getUnit()->getName());
        $this->assertEquals($this->sum*100, $s1->getSum()->getAmount());
        $this->assertSame($this->currency, $s1->getSum()->getCurrency()->getCode());
        $this->assertSame($s1, $s2);
        $this->assertSame($s1, $s3);
        $this->assertSame($s1, $s4);
        $this->assertSame($s1, $s5);
    }

    public function testGetAction()
    {
        $this->testGetSale();
        $s1 = $this->factory->get('action', [
            'id' => $this->actionId,
            'type' => $this->type,
            'target' => $this->targetId,
            'customer' => $this->user,
            'quantity' => $this->quantity . ' ' . $this->unit,
            'sale' => $this->saleId,
            'time' => $this->time,
        ]);
        $s2 = $this->factory->get('action', ['id' => $this->actionId, 'target' => $this->targetId]);
        $s3 = $this->factory->get('action', ['id' => $this->actionId]);
        $s4 = $this->factory->get('action', $this->actionId);
        $s5 = $this->factory->find('action', [$this->actionId]);
        $this->assertInstanceOf(Action::class, $s1);
        $this->assertSame($this->actionId, $s1->getId());
        $this->assertSame($this->user, $s1->getCustomer()->getLogin());
        $this->assertSame($this->time, $s1->getTime()->format('c'));
        $this->assertSame($this->type, $s1->getType()->getName());
        $this->assertSame($this->targetId, $s1->getTarget()->getId());
        $this->assertSame($this->saleId, $s1->getSale()->getId());
        $this->assertSame($s1, $s2);
        $this->assertSame($s1, $s3);
        $this->assertSame($s1, $s4);
        $this->assertSame($s1, $s5);
    }

    public function testGetPrice()
    {
        $p1 = $this->factory->get('price', [
            'id' => $this->priceId,
            'type' => $this->type,
            'target' => $this->targetId,
            'price' => $this->sum . ' ' . $this->currency,
            'prepaid' => '0 ' . $this->unit,
            'currency' => $this->currency,
        ]);
        $p2 = $this->factory->get('price', ['id' => $this->priceId, 'target' => $this->targetId]);
        $p3 = $this->factory->get('price', ['id' => $this->priceId]);
        $p4 = $this->factory->get('price', $this->priceId);
        $p5 = $this->factory->find('price', [$this->priceId]);
        $this->assertInstanceOf(PriceInterface::class, $p1);
        $this->assertSame($this->priceId, $p1->getId());
        $this->assertSame($this->type, $p1->getType()->getName());
        $this->assertSame($this->targetId, $p1->getTarget()->getId());
        $this->assertSame($this->unit, $p1->getPrepaid()->getUnit()->getName());
        $this->assertEquals($this->sum*100, $p1->getPrice()->getAmount());
        $this->assertSame($p1, $p2);
        $this->assertSame($p1, $p3);
        $this->assertSame($p1, $p4);
        $this->assertSame($p1, $p5);
    }

    public function testGetMoney()
    {
        $str = $this->sum . ' ' . $this->currency;
        $m1 = $this->factory->get('money', ['amount' => $this->sum*100, 'currency' => $this->currency]);
        $m2 = $this->factory->get('money', $str);
        //$m3 = $this->factory->find('money', [$str]);
        $this->assertEquals($this->sum*100, $m1->getAmount());
        $this->assertSame($this->currency, $m1->getCurrency()->getCode());
        $this->assertSame($m1, $m2);
        //$this->assertSame($m1, $m3);
    }

    public function testGetQuantity()
    {
        $str = $this->quantity . ' ' . $this->unit;
        $m1 = $this->factory->get('quantity', ['quantity' => $this->quantity, 'unit' => $this->unit]);
        $m2 = $this->factory->get('quantity', $str);
        $m3 = $this->factory->find('quantity', [$str]);
        $this->assertSame($this->quantity, $m1->getQuantity());
        $this->assertSame($this->unit, $m1->getUnit()->getName());
        $this->assertSame($m1, $m2);
        $this->assertSame($m1, $m3);
    }

    public function testGetUnit()
    {
        $u1 = $this->factory->get('unit', ['name' => $this->unit]);
        $u2 = $this->factory->get('unit', ['name' => $this->unit]);
        $u3 = $this->factory->get('unit', ['name' => $this->unit]);
        $u4 = $this->factory->get('unit', $this->unit);
        $u5 = $this->factory->find('unit', [$this->unit]);
        $this->assertInstanceOf(Unit::class, $u1);
        $this->assertSame($this->unit, $u1->getName());
        $this->assertSame($u1, $u2);
        $this->assertSame($u1, $u3);
        $this->assertSame($u1, $u4);
        $this->assertSame($u1, $u5);
    }

    public function testGetTime()
    {
        $u1 = $this->factory->get('time', ['date' => $this->time]);
        $u2 = $this->factory->get('time', ['date' => $this->time]);
        $u3 = $this->factory->get('time', ['date' => $this->time]);
        $u4 = $this->factory->get('time', $this->time);
        $u5 = $this->factory->find('time', [$this->time]);
        $this->assertInstanceOf(DateTimeImmutable::class, $u1);
        $this->assertSame($this->time, $u1->format('c'));
        $this->assertSame($u1, $u2);
        $this->assertSame($u1, $u3);
        $this->assertSame($u1, $u4);
        $this->assertSame($u1, $u5);
    }
}
