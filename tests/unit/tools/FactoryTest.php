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

use hiqdev\php\billing\action\Action;
use hiqdev\php\billing\charge\Generalizer;
use hiqdev\php\billing\order\Calculator;
use hiqdev\php\billing\order\CalculatorInterface;
use hiqdev\php\billing\order\Order;
use hiqdev\php\billing\order\OrderInterface;
use hiqdev\php\billing\tests\unit\plan\CertificatePlan;
use hiqdev\php\billing\tools\Factory;
use hiqdev\php\billing\customer\Customer;
use hiqdev\php\billing\customer\CustomerFactory;
use hiqdev\php\billing\plan\Plan;
use hiqdev\php\billing\plan\PlanFactory;

class FactoryTest extends \PHPUnit\Framework\TestCase
{
    private $user = 'user';
    private $reseller = 'reseller';
    private $plan = 'plan';

    protected function setUp()
    {
        $this->factory = new Factory([
            'plan' => new PlanFactory(),
            'customer' => new CustomerFactory(),
        ]);
    }

    public function testGetCustomer()
    {
        $c1 = $this->factory->get('customer', ['login' => $this->user, 'seller' => $this->reseller]);
        $c2 = $this->factory->get('customer', ['login' => $this->user, 'seller' => $this->reseller]);
        $c3 = $this->factory->get('customer', ['login' => $this->user]);
        $c4 = $this->factory->get('customer', $this->user);
        $c4 = $this->factory->find('customer', [$this->user]);
        $this->assertInstanceOf(Customer::class, $c1);
        $this->assertSame($this->user, $c1->getLogin());
        $this->assertSame($this->reseller, $c1->getSeller()->getLogin());
        $this->assertSame($c1, $c2);
        $this->assertSame($c2, $c3);
        $this->assertSame($c3, $c4);
    }

    public function testGetPlan()
    {
        $p1 = $this->factory->get('plan', ['name' => $this->plan, 'seller' => $this->reseller]);
        $p2 = $this->factory->get('plan', ['name' => $this->plan, 'seller' => $this->reseller]);
        $p3 = $this->factory->get('plan', ['name' => $this->plan]);
        $p4 = $this->factory->get('plan', $this->plan);
        $p4 = $this->factory->find('plan', [$this->plan]);
        $this->assertInstanceOf(Plan::class, $p1);
        $this->assertSame($this->plan, $p1->getName());
        $this->assertSame($this->reseller, $p1->getSeller()->getLogin());
        $this->assertSame($p1, $p2);
        $this->assertSame($p2, $p3);
        $this->assertSame($p3, $p4);
    }
}
