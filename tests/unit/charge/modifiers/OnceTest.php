<?php declare(strict_types=1);

namespace hiqdev\php\billing\tests\unit\charge\modifiers;

use DateTimeImmutable;
use hiqdev\php\billing\action\Action;
use hiqdev\php\billing\action\ActionInterface;
use hiqdev\php\billing\charge\modifiers\addons\MonthPeriod;
use hiqdev\php\billing\charge\modifiers\addons\YearPeriod;
use hiqdev\php\billing\charge\modifiers\exception\OnceException;
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

    /**
     * @dataProvider periodCreationProvider
     */
    public function testPeriodCreation(string $interval, string $expectedClass, int $expectedValue): void
    {
        $oncePeriod = $this->buildOnce($interval);
        $period = $oncePeriod->getPer();
        $this->assertInstanceOf($expectedClass, $period);
        $this->assertSame($expectedValue, $period->getValue());
    }

    public function periodCreationProvider(): array
    {
        return [
            'yearly' => ['1 year', YearPeriod::class, 1],
            'quarterly' => ['3 months', MonthPeriod::class, 3],
        ];
    }

    protected function buildOnce(string $interval): Once
    {
        return (new Once())->per($interval);
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
        yield ['days'];
        yield ['1 day'];
        yield ['1.5 day'];
        yield ['2.5 days'];
        yield ['3.5 month'];
        yield ['1.5 years'];
        yield ['1.5 year'];
        yield ['invalid'];
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

        $saleTime = new DateTimeImmutable('22-11-2023');
        $actionTime = $saleTime->modify('+1 year');
        $action = $this->createActionWithSale($this->prepaid->multiply(2), $actionTime, $saleTime);
        $type = $this->createType('monthly,monthly');
        $price = $this->createPrice($type);

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

    public function testPerOneYear_With11MonthsLaterShouldReturnZeroCharge(): void
    {
        $once = $this->buildOnce('1 year');

        $saleTime = new DateTimeImmutable('22-10-2023');
        $actionTime = $saleTime->modify('+11 months');
        $action = $this->createActionWithSale($this->prepaid->multiply(2), $actionTime, $saleTime);
        $type = $this->createType('monthly,monthly');
        $price = $this->createPrice($type);

        $charge = $this->calculator->calculateCharge($price, $action);

        $charges = $once->modifyCharge($charge, $action);
        $this->assertCount(0, $charges);
    }

    private function createActionWithSale(
        QuantityInterface $quantity,
        DateTimeImmutable $actionTime,
        DateTimeImmutable $saleTime
    ): ActionInterface {
        $action = $this->createActionWithCustomTime($quantity, $actionTime);

        $plan = new Plan(null, '', $this->customer, [$this->price]);
        $sale = new Sale(null, $this->target, $this->customer, $plan, $saleTime);
        $action->setSale($sale);

        return $action;
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

    public function testPerThreeMonths_After3Months_ShouldApplyCharge(): void
    {
        $once = $this->buildOnce('3 months');

        $saleTime = new DateTimeImmutable('22-01-2024');
        $actionTime = $saleTime->modify('+3 months');
        $action = $this->createActionWithSale($this->prepaid->multiply(2), $actionTime, $saleTime);
        $type = $this->createType('monthly,monthly');
        $price = $this->createPrice($type);

        $charge = $this->calculator->calculateCharge($price, $action);

        $charges = $once->modifyCharge($charge, $action);
        $this->assertIsArray($charges);
        $this->assertCount(1, $charges);
        $this->assertSame($charge, $charges[0]);
    }

    public function testPerThreeMonths_After2Months_ShouldReturnZeroCharge(): void
    {
        $once = $this->buildOnce('3 months');

        $saleTime = new DateTimeImmutable('22-01-2024');
        $actionTime = $saleTime->modify('+2 months');
        $action = $this->createActionWithSale($this->prepaid->multiply(2), $actionTime, $saleTime);
        $type = $this->createType('monthly,monthly');
        $price = $this->createPrice($type);

        $charge = $this->calculator->calculateCharge($price, $action);

        $charges = $once->modifyCharge($charge, $action);
        $this->assertCount(0, $charges);
    }
}
