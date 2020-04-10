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

use hiqdev\php\billing\tools\Factory;
use hiqdev\php\billing\customer\Customer;
use hiqdev\php\billing\customer\CustomerFactory;
use hiqdev\php\billing\type\Type;
use hiqdev\php\billing\type\TypeFactory;
use hiqdev\php\billing\target\Target;
use hiqdev\php\billing\target\TargetFactory;
use hiqdev\php\billing\plan\Plan;
use hiqdev\php\billing\plan\PlanFactory;
use hiqdev\php\billing\price\PriceInterface;
use hiqdev\php\billing\price\PriceFactory;
use hiqdev\php\billing\price\SinglePrice;
use hiqdev\php\units\Unit;

class FactoryTest extends \PHPUnit\Framework\TestCase
{
    private $unit = 'items';
    private $quantity = '10';
    private $currency = 'USD';

    private $user = 'user';
    private $reseller = 'reseller';

    private $planId = 'plan-id';
    private $plan = 'plan';

    private $type = 'type';
    private $typeId = 'type-id';

    private $priceId = 'price-id';
    private $priceSum = '11.99';

    private $target = 'target';
    private $targetId = 'target-id';

    protected function setUp()
    {
        $this->factory = new Factory([
            'type'      => new TypeFactory(),
            'plan'      => new PlanFactory(),
            'price'     => new PriceFactory([], SinglePrice::class),
            'target'    => new TargetFactory(),
            'customer'  => new CustomerFactory(),
        ]);
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
        $t1 = $this->factory->get('target', ['id' => $this->targetId, 'name' => $this->target, 'type' => 'simple']);
        $t2 = $this->factory->get('target', ['id' => $this->targetId, 'name' => $this->target, 'type' => 'simple']);
        $t3 = $this->factory->get('target', ['id' => $this->targetId]);
        $t4 = $this->factory->get('target', $this->targetId);
        $t5 = $this->factory->find('target', [$this->targetId]);
        $this->assertInstanceOf(Target::class, $t1);
        $this->assertSame($this->target, $t1->getName());
        $this->assertSame($this->targetId, $t1->getId());
        $this->assertSame($t1, $t2);
        $this->assertSame($t1, $t3);
        $this->assertSame($t1, $t4);
        $this->assertSame($t1, $t5);
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

    public function testGetPrice()
    {
        $p1 = $this->factory->get('price', [
            'id' => $this->priceId,
            'type' => $this->type,
            'target' => $this->targetId,
            'price' => $this->priceSum . ' ' . $this->currency,
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
        $this->assertEquals($this->priceSum*100, $p1->getPrice()->getAmount());
        $this->assertSame($p1, $p2);
        $this->assertSame($p1, $p3);
        $this->assertSame($p1, $p4);
        $this->assertSame($p1, $p5);
    }

    public function testGetMoney()
    {
        $str = $this->priceSum . ' ' . $this->currency;
        $m1 = $this->factory->get('money', ['amount' => $this->priceSum, 'currency' => $this->currency]);
        $m2 = $this->factory->get('money', $str);
        $m3 = $this->factory->find('money', [$str]);
        $this->assertEquals($this->priceSum*100, $m1->getAmount());
        $this->assertSame($this->currency, $m1->getCurrency()->getCode());
        $this->assertSame($m1, $m2);
        $this->assertSame($m1, $m3);
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

}
