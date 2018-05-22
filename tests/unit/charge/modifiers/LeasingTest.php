<?php
/**
 * PHP Billing Library
 *
 * @link      https://github.com/hiqdev/php-billing
 * @package   php-billing
 * @license   BSD-3-Clause
 * @copyright Copyright (c) 2017-2018, HiQDev (http://hiqdev.com/)
 */

namespace hiqdev\php\billing\tests\unit\charge\modifiers;

use DateTimeImmutable;
use hiqdev\php\billing\charge\Charge;
use hiqdev\php\billing\charge\modifiers\Leasing;
use hiqdev\php\billing\charge\modifiers\addons\MonthPeriod;
use hiqdev\php\billing\charge\modifiers\addons\YearPeriod;
use hiqdev\php\billing\tests\unit\action\ActionTest;
use hiqdev\php\units\Quantity;

/**
 * @author Andrii Vasyliev <sol@hiqdev.com>
 */
class LeasingTest extends ActionTest
{
    protected function buildLeasing($term)
    {
        $month = (new DateTimeImmutable())->modify('first day of this month midnight');

        return (new Leasing())->lasts($term);
    }

    public function testCreateMonth()
    {
        $leasing = $this->buildLeasing('12 months');
        $period = $leasing->getTerm();
        $this->assertInstanceOf(MonthPeriod::class, $period);
        $this->assertSame(12, $period->getValue());
    }

    public function testCreateYear()
    {
        $leasing = $this->buildLeasing('1 year');
        $period = $leasing->getTerm();
        $this->assertInstanceOf(YearPeriod::class, $period);
        $this->assertSame(1, $period->getValue());
    }
}
