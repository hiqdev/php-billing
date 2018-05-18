<?php
/**
 * PHP Billing Library
 *
 * @link      https://github.com/hiqdev/php-billing
 * @package   php-billing
 * @license   BSD-3-Clause
 * @copyright Copyright (c) 2017-2018, HiQDev (http://hiqdev.com/)
 */

namespace hiqdev\php\billing\charge\modifiers\addons;

use DateTimeImmutable;

/**
 * Month Period addon.
 *
 * @author Andrii Vasyliev <sol@hiqdev.com>
 */
class MonthPeriod extends Period
{
    public static function countPeriodsPassed(DateTimeImmutable $since, DateTimeImmutable $time)
    {
        $diff = $time->diff($since);

        return $diff->m + $diff->y*12;
    }
}
