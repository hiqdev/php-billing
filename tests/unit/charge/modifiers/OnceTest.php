<?php declare(strict_types=1);

namespace hiqdev\php\billing\tests\unit\charge\modifiers;

use DateTimeImmutable;
use hiqdev\php\billing\action\Action;
use hiqdev\php\billing\action\ActionInterface;
use hiqdev\php\billing\charge\modifiers\addons\MonthPeriod;
use hiqdev\php\billing\charge\modifiers\addons\YearPeriod;
use hiqdev\php\billing\charge\modifiers\Once;
use hiqdev\php\billing\formula\FormulaEngineException;
use hiqdev\php\billing\plan\Plan;
use hiqdev\php\billing\price\PriceInterface;
use hiqdev\php\billing\price\SinglePrice;
use hiqdev\php\billing\sale\Sale;
use hiqdev\php\billing\tests\unit\action\ActionTest;
use hiqdev\php\billing\type\Type;
use hiqdev\php\billing\type\TypeInterface;
use hiqdev\php\units\Quantity;
use hiqdev\php\units\QuantityInterface;

class OnceTest extends ActionTest
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->prepaid = Quantity::item(0);
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

    #[\PHPUnit\Framework\Attributes\DataProvider('periodCreationProvider')]
    public function testPeriodCreation(string $interval, string $expectedClass, int $expectedValue): void
    {
        $oncePeriod = $this->buildOnce($interval);
        $period = $oncePeriod->getPer();
        $this->assertInstanceOf($expectedClass, $period);
        $this->assertSame($expectedValue, $period->getValue());
    }

    public static function periodCreationProvider(): array
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

    #[\PHPUnit\Framework\Attributes\DataProvider('fractionDataProvider')]
    public function testFraction(string $interval): void
    {
        $this->expectException(FormulaEngineException::class);

        $this->buildOnce($interval);
    }

    public static function fractionDataProvider(): iterable
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

    public function testPerOneYear_WithOneYearLaterShouldApplyCharge(): void
    {
        $once = $this->buildOnce('1 year');

        $saleTime = new DateTimeImmutable('22-11-2023');
        $actionTime = $saleTime->modify('+1 year');
        $action = $this->createActionWithSale($actionTime, $saleTime);
        $type = $this->createType('monthly,monthly');
        $price = $this->createPrice($type);

        $charge = $this->calculator->calculateCharge($price, $action);

        $charges = $once->modifyCharge($charge, $action);
        $this->assertIsArray($charges);
        $this->assertCount(1, $charges);
        $this->assertSame($charge, $charges[0]);
    }

    public function testPerOneYear_ShouldChargeInFirstMonthOfSale(): void
    {
        $once = $this->buildOnce('1 year');
        $saleTime = new DateTimeImmutable('01-01-2025 12:31:00');
        $actionTime = $saleTime->modify('first day of this month 00:00:00');
        $action = $this->createActionWithSale($actionTime, $saleTime);
        $type = $this->createType('monthly,monthly');
        $price = $this->createPrice($type);

        $charge = $this->calculator->calculateCharge($price, $action);
        $this->assertNonZeroCharge($charge);

        $charges = $once->modifyCharge($charge, $action);
        $this->assertIsArray($charges);
        $this->assertCount(1, $charges);
        $this->assertSame($charge, $charges[0]);
        $this->assertNonZeroCharge($charges[0]);
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
        $action = $this->createActionWithSale($actionTime, $saleTime);
        $type = $this->createType('monthly,monthly');
        $price = $this->createPrice($type);

        $charge = $this->calculator->calculateCharge($price, $action);
        $this->assertNonZeroCharge($charge);

        $charges = $once->modifyCharge($charge, $action);
        $this->assertCount(1, $charges);
        $this->assertZeroCharge($charges[0]);
    }

    private function createActionWithSale(
        DateTimeImmutable $actionTime,
        DateTimeImmutable $saleTime
    ): ActionInterface {
        $quantity = Quantity::item(1);
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

        $this->expectException(FormulaEngineException::class);
        $once->modifyCharge($charge, $action);
    }

    public function testPerThreeMonths_After3Months_ShouldApplyCharge(): void
    {
        $once = $this->buildOnce('3 months');

        $saleTime = new DateTimeImmutable('22-01-2024');
        $actionTime = $saleTime->modify('+3 months');
        $action = $this->createActionWithSale($actionTime, $saleTime);
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
        $action = $this->createActionWithSale($actionTime, $saleTime);
        $type = $this->createType('monthly,monthly');
        $price = $this->createPrice($type);

        $charge = $this->calculator->calculateCharge($price, $action);

        $charges = $once->modifyCharge($charge, $action);
        $this->assertCount(1, $charges);
        $this->assertZeroCharge($charges[0]);
    }

    public function testPerOneYearSinceDate(): void
    {
        $once = $this->buildOnce('1 year')->since('04.2025');

        $saleTime = new DateTimeImmutable('2023-01-01'); // Sale time should be ignored, since is set
        $price = $this->createPrice($this->createType('monthly,monthly'));

        // Action in the same month and year as since: applies
        $actionTime1 = new DateTimeImmutable('2025-04-30');
        $action1 = $this->createActionWithSale($actionTime1, $saleTime);
        $charge1 = $this->calculator->calculateCharge($price, $action1);
        $charges1 = $once->modifyCharge($charge1, $action1);
        $this->assertCount(1, $charges1);
        $this->assertEquals($charge1, $charges1[0]);

        // Action one year later in same month: applies
        $actionTime2 = new DateTimeImmutable('2026-04-01');
        $action2 = $this->createActionWithSale($actionTime2, $saleTime);
        $charge2 = $this->calculator->calculateCharge($price, $action2);
        $charges2 = $once->modifyCharge($charge2, $action2);
        $this->assertCount(1, $charges2);
        $this->assertEquals($charge2, $charges2[0]);

        // Action one year later in different month: zero charge
        $actionTime3 = new DateTimeImmutable('2026-05-01');
        $action3 = $this->createActionWithSale($actionTime3, $saleTime);
        $charge3 = $this->calculator->calculateCharge($price, $action3);
        $charges3 = $once->modifyCharge($charge3, $action3);
        $this->assertCount(1, $charges3);
        $this->assertZeroCharge($charges3[0]);
        
        // Action before since date: zero charge
        $actionTime4 = new DateTimeImmutable('2025-03-01');
        $action4 = $this->createActionWithSale($actionTime4, $saleTime);
        $charge4 = $this->calculator->calculateCharge($price, $action4);
        $charges4 = $once->modifyCharge($charge4, $action4);
        $this->assertCount(1, $charges4);
        $this->assertZeroCharge($charges4[0]);
    }
}
