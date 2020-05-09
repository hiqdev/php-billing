<?php
/**
 * PHP Billing Library
 *
 * @link      https://github.com/hiqdev/php-billing
 * @package   php-billing
 * @license   BSD-3-Clause
 * @copyright Copyright (c) 2017-2020, HiQDev (http://hiqdev.com/)
 */

namespace hiqdev\php\billing\tests\unit\charge\modifiers\addons;

use DateTimeImmutable;
use hiqdev\php\billing\charge\modifiers\addons\Date;

/**
 * @author Andrii Vasyliev <sol@hiqdev.com>
 */
class DateTest extends \PHPUnit\Framework\TestCase
{
    protected $date;
    protected $year = '2018';
    protected $month = '11';

    protected function setUp()
    {
        $this->date = new DateTimeImmutable($this->year . '-' . $this->month . '-01');
    }

    public function testEnsureValidValue()
    {
        $year = $this->year;
        $month = $this->month;
        $this->assertEquals($this->date, Date::ensureValidValue("$year-$month"));
        $this->assertEquals($this->date, Date::ensureValidValue("$year-$month-01"));
        $this->assertEquals($this->date, Date::ensureValidValue("$month.$year"));
        $this->assertEquals($this->date, Date::ensureValidValue("1.$month.$year"));
    }
}
