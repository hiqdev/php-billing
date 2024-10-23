<?php declare(strict_types=1);

namespace hiqdev\php\billing\tests\unit\charge\modifiers;

use DateTimeImmutable;
use hiqdev\php\billing\action\Action;
use hiqdev\php\billing\charge\modifiers\addons\MonthPeriod;
use hiqdev\php\billing\charge\modifiers\addons\YearPeriod;
use hiqdev\php\billing\charge\modifiers\exception\OnceException;
use hiqdev\php\billing\charge\modifiers\Installment;
use hiqdev\php\billing\charge\modifiers\Once;
use hiqdev\php\billing\plan\Plan;
use hiqdev\php\billing\price\PriceInterface;
use hiqdev\php\billing\price\SinglePrice;
use hiqdev\php\billing\sale\Sale;
use hiqdev\php\billing\tests\unit\action\ActionTest;
use hiqdev\php\billing\type\Type;
use hiqdev\php\billing\type\TypeInterface;
use hiqdev\php\units\QuantityInterface;

class OnceTest extends ActionTest
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->type = $this->createType('monthly,monthly');
        $this->price = $this->createPrice($this->type);
    }

    private function createType(string $name): TypeInterface
    {
        return Type::anyId($name);
    }

    private function createPrice(TypeInterface $type): PriceInterface
    {
        return new SinglePrice(5, $type, $this->target, null, $this->prepaid, $this->money);
    }

    public function testCreateYear(): void
    {
        $oncePeriod = $this->buildOnce('1 year');
        $period = $oncePeriod->getPer();
        $this->assertInstanceOf(YearPeriod::class, $period);
        $this->assertSame(1, $period->getValue());
    }

    protected function buildOnce(string $interval): Once
    {
        return (new Once())->per($interval);
    }

    public function testCreateOnePerThreeMonths(): void
    {
        $oncePeriod = $this->buildOnce('3 months');
        $period = $oncePeriod->getPer();
        $this->assertInstanceOf(MonthPeriod::class, $period);
        $this->assertSame(3, $period->getValue());
    }

    /**
     * @dataProvider fractionDataProvider
     */
    public function testFraction(string $interval): void
    {
        $this->expectException(OnceException::class);

        $this->buildOnce($interval);
    }

    private function fractionDataProvider(): iterable
    {
        yield ['1.5 months'];
        yield ['day'];
    }

    public function testOverusePricingException(): void
    {
        $oncePeriod = $this->buildOnce('6 months');
        $action = $this->createAction($this->prepaid->multiply(2));
        $type = Type::anyId('overuse,cloud_ip_regular_max');
        $price = $this->createPrice($type);
        $charge = $this->calculator->calculateCharge($price, $action);

        $this->expectException(OnceException::class);
        $oncePeriod->modifyCharge($charge, $action);
    }

    public function testPerOneYear_WithOneYearLaterShouldApplyCharge(): void
    {
        $once = $this->buildOnce('1 year');

        $time = new DateTimeImmutable('22-11-2024');
        $action = $this->createActionWithCustomTime($this->prepaid->multiply(2), $time);
        $type = $this->createType('monthly,monthly');
        $price = $this->createPrice($type);

        $plan = new Plan(null, '', $this->customer, [$this->price]);
        $sale = new Sale(null, $this->target, $this->customer, $plan, new DateTimeImmutable('22-11-2023'));
        $action->setSale($sale);

        $charge = $this->calculator->calculateCharge($price, $action);

        $charges = $once->modifyCharge($charge, $action);
        $this->assertIsArray($charges);
        $this->assertCount(1, $charges);
        $this->assertSame($charge, $charges[0]);
    }

    private function createActionWithCustomTime(QuantityInterface $quantity, DateTimeImmutable $time): Action
    {
        return new Action(null, $this->type, $this->target, $quantity, $this->customer, $time);
    }

    public function testPerOneYear_WithOneYearLaterShouldApplyChar(): void
    {
        $once = $this->createOnce('1 year');

        // 1 year later should apply charge
        $chargeResult = $once->modifyCharge($this->charge, $this->action);
        $this->assertCount(1, $chargeResult);
        $this->assertSame($this->charge, $chargeResult[0]);

        // 11 months later should return zero charge
        $this->moveSaleDateByMonths(11);
        $chargeResult = $once->modifyCharge($this->charge, $this->action);
        $this->assertCount(1, $chargeResult);
        $this->assertEquals(0, $chargeResult[0]->getPrice());
    }

    public function testModifyCharge_WithActionWithoutSale_ThrowsException(): void
    {
        $once = $this->buildOnce('1 year');

        $action = $this->createAction($this->prepaid->multiply(2));
        $type = $this->createType('monthly,monthly');
        $price = $this->createPrice($type);
        $charge = $this->calculator->calculateCharge($price, $action);

        $this->expectException(OnceException::class);
        $once->modifyCharge($charge, $action);
    }

    // TODO: test action without sale time

    public function testPerThreeMonths(): void
    {
        $once = new Once();
        $once->per('3 months');

        // After 3 months, should apply charge
        $this->moveSaleDateByMonths(3);
        $chargeResult = $once->modifyCharge($this->charge, $this->action);
        $this->assertCount(1, $chargeResult);
        $this->assertSame($this->charge, $chargeResult[0]);

        // After 2 months, should return zero charge
        $this->moveSaleDateByMonths(2);
        $chargeResult = $once->modifyCharge($this->charge, $this->action);
        $this->assertCount(1, $chargeResult);
        $this->assertEquals(0, $chargeResult[0]->getPrice());
    }

    public function testSaleReopen(): void
    {
        $once = new Once();
        $once->per('1 year');

        // Initially should apply charge
        $this->moveSaleDateByMonths(12);
        $chargeResult = $once->modifyCharge($this->charge, $this->action);
        $this->assertCount(1, $chargeResult);
        $this->assertSame($this->charge, $chargeResult[0]);

        // Simulate sale re-open
        $this->moveSaleDateByMonths(-12); // Reset the sale date
        $chargeResult = $once->modifyCharge($this->charge, $this->action);
        $this->assertCount(1, $chargeResult);
        $this->assertEquals(0, $chargeResult[0]->getPrice()); // Now it should give a zero charge
    }

    private function moveSaleDateByMonths(int $months): void
    {
        $sale = $this->action->getSale();
        $time = clone $sale->getTime();
        $time->modify("{$months} months");
        $sale->method('getTime')->willReturn($time);
    }
}
