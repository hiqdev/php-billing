<?php
/**
 * PHP Billing Library
 *
 * @link      https://github.com/hiqdev/php-billing
 * @package   php-billing
 * @license   BSD-3-Clause
 * @copyright Copyright (c) 2017-2020, HiQDev (http://hiqdev.com/)
 */

namespace hiqdev\php\billing\charge\modifiers\addons;

use DateInterval;
use DateTimeImmutable;

/**
 * Day period addon
 *
 * @author Dmytro Naumenko <d.naumenko.a@gmail.com>
 */
class DayPeriod extends Period
{
    public function countPeriodsPassed(DateTimeImmutable $since, DateTimeImmutable $time): float
    {
        $diff = $time->diff($since);

        return $diff->format('%r%a') / $this->value;
    }

    public function addTo(DateTimeImmutable $since): DateTimeImmutable
    {
        return $since->add(new DateInterval("P{$this->value}D"));
    }
}
