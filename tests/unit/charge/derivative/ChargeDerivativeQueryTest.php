<?php

declare(strict_types=1);

namespace hiqdev\php\billing\tests\unit\charge\addons;

use hiqdev\php\billing\action\Action;
use hiqdev\php\billing\bill\Bill;
use hiqdev\php\billing\charge\Charge;
use hiqdev\php\billing\charge\derivative\ChargeDerivativeQuery;
use hiqdev\php\billing\price\SinglePrice;
use hiqdev\php\billing\target\Target;
use hiqdev\php\billing\target\TargetInterface;
use hiqdev\php\billing\type\Type;
use Money\Currency;
use Money\Money;
use PHPUnit\Framework\TestCase;

/**
 * Class ChargeDerivativeQueryTest
 *
 * @author Dmytro Naumenko <d.naumenko.a@gmail.com>
 * @covers ChargeDerivativeQuery
 */
class ChargeDerivativeQueryTest extends TestCase
{
    protected ChargeDerivativeQuery $query;

    protected function setUp(): void
    {
        parent::setUp();
        $this->query = $this->createQuery();
    }

    protected function createQuery(): ChargeDerivativeQuery
    {
        return new ChargeDerivativeQuery();
    }

    public function testChangeId()
    {
        $this->query->changeId(1);
        $this->assertEquals(1, $this->query->getId());
    }

    public function testChangeTarget()
    {
        $query = $this->query;
        $target = new Target(TargetInterface::ANY, 'foo');
        $this->assertFalse($query->isChanged('target'));
        $query->changeTarget($target);
        $this->assertTrue($query->isChanged('target'));
        $this->assertEquals($target, $query->getTarget());
    }

    public function testChangeUsage()
    {
        $query = $this->query;
        $target = new Target(TargetInterface::ANY, 'foo');
        $this->assertFalse($query->isChanged('usage'));
        $query->changeTarget($target);
        $this->assertTrue($query->isChanged('target'));
        $this->assertEquals($target, $query->getTarget());
    }

    public function testGet()
    {
        $query = $this->query;

        $sum = new Money(0, new Currency('USD'));
        $this->assertSame($sum, $query->get('sum', $sum));
        $this->assertNull($query->get('sum'));
    }

    public function testChangeSum()
    {
        $query = $this->query;
        $this->assertFalse($query->isChanged('sum'));
        $sum = new Money(0, new Currency('USD'));
        $query->changeSum($sum);
        $this->assertTrue($query->isChanged('sum'));
        $this->assertEquals($sum, $query->getSum());
    }

    public function testChangeParent()
    {
        $query = $this->query;
        $this->assertFalse($query->isChanged('parent'));

        /** @var Charge $parent */
        $parent = (new \ReflectionClass(Charge::class))->newInstanceWithoutConstructor();

        $query->changeParent($parent);
        $this->assertTrue($query->isChanged('parent'));
        $this->assertEquals($parent, $query->getParent());
    }

    public function testChangeType()
    {
        $query = $this->query;
        $this->assertFalse($query->isChanged('type'));
        $type = Type::anyId('foo');
        $query->changeType($type);
        $this->assertTrue($query->isChanged('type'));
        $this->assertEquals($type, $query->getType());
    }

    public function testChangeComment()
    {
        $query = $this->query;
        $this->assertFalse($query->isChanged('comment'));
        $query->changeComment('Test comment');
        $this->assertTrue($query->isChanged('comment'));
        $this->assertEquals('Test comment', $query->getComment());
    }

    public function testChangePrice()
    {
        $query = $this->query;
        $this->assertFalse($query->isChanged('price'));

        /** @var SinglePrice $price */
        $price = (new \ReflectionClass(SinglePrice::class))->newInstanceWithoutConstructor();

        $query->changePrice($price);
        $this->assertTrue($query->isChanged('price'));
        $this->assertSame($price, $query->getPrice());
    }

    public function testChangeAction()
    {
        $query = $this->query;
        $this->assertFalse($query->isChanged('action'));

        /** @var Action $action */
        $action = (new \ReflectionClass(Action::class))->newInstanceWithoutConstructor();

        $query->changeAction($action);
        $this->assertTrue($query->isChanged('action'));
        $this->assertSame($action, $query->getAction());
    }


    public function testChangeBill()
    {
        $query = $this->query;
        $this->assertFalse($query->isChanged('bill'));

        /** @var Bill $bill */
        $bill = (new \ReflectionClass(Bill::class))->newInstanceWithoutConstructor();

        $query->changeBill($bill);
        $this->assertTrue($query->isChanged('bill'));
        $this->assertSame($bill, $query->getBill());
    }
}
