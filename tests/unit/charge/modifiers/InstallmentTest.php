<?php
/**
 * PHP Billing Library
 *
 * @link      https://github.com/hiqdev/php-billing
 * @package   php-billing
 * @license   BSD-3-Clause
 * @copyright Copyright (c) 2017-2020, HiQDev (http://hiqdev.com/)
 */

namespace hiqdev\php\billing\tests\unit\charge\modifiers;

use DateTimeImmutable;
use hiqdev\php\billing\charge\modifiers\addons\MonthPeriod;
use hiqdev\php\billing\charge\modifiers\addons\YearPeriod;
use hiqdev\php\billing\charge\modifiers\event\InstallmentWasStarted;
use hiqdev\php\billing\charge\modifiers\Installment;
use hiqdev\php\billing\price\SinglePrice;
use hiqdev\php\billing\tests\unit\action\ActionTest;
use hiqdev\php\billing\type\Type;

/**
 * @author Andrii Vasyliev <sol@hiqdev.com>
 */
class InstallmentTest extends ActionTest
{
    protected $reason = 'test reason string';

    protected function setUp(): void
    {
        parent::setUp();
        $this->type = Type::anyId('monthly,installment');
        $this->price = new SinglePrice(5, $this->type, $this->target, null, $this->prepaid, $this->money);
    }

    protected function buildInstallment($term)
    {
        $month = (new DateTimeImmutable())->modify('first day of this month midnight');

        return (new Installment())->since($month)->lasts($term);
    }

    public function testCreateMonth()
    {
        $installment = $this->buildInstallment('12 months');
        $period = $installment->getTerm();
        $this->assertInstanceOf(MonthPeriod::class, $period);
        $this->assertSame(12, $period->getValue());
    }

    public function testTill()
    {
        $this->expectException(\Exception::class);
        $this->buildInstallment('month')->till('08.2024');
    }

    public function testReason()
    {
        $installment = $this->buildInstallment('12 months');
        $installment = $installment->reason($this->reason);
        $this->assertSame($this->reason, $installment->getReason()->getValue());
    }

    public function testCreateYear()
    {
        $installment = $this->buildInstallment('1 year');
        $period = $installment->getTerm();
        $this->assertInstanceOf(YearPeriod::class, $period);
        $this->assertSame(1, $period->getValue());
    }

    public function testModifyCharge()
    {
        $installment = $this->buildInstallment('6 months');
        $action = $this->createAction($this->prepaid->multiply(2));
        $charge = $this->calculator->calculateCharge($this->price, $action);
        $charges = $installment->modifyCharge($charge, $action);
        $event = $charges[0]->releaseEvents()[0];
        $this->assertInstanceOf(InstallmentWasStarted::class, $event);
        $this->assertIsArray($charges);
        $this->assertSame(1, count($charges));
        $this->assertEquals($charge, $charges[0]);
    }
}
