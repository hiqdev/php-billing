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

use DateTimeImmutable;

/**
 * Year Period addon.
 *
 * @author Andrii Vasyliev <sol@hiqdev.com>
 */
class YearPeriod extends Period
{
    public function countPeriodsPassed(DateTimeImmutable $since, DateTimeImmutable $time): float
    {
        $diff = $time->diff($since);

        return $diff->y / $this->value;
    }

    public function addTo(DateTimeImmutable $since): DateTimeImmutable
    {
        return $since->add(new \DateInterval("P{$this->value}Y"));
    }

    public function __toString(): string
    {
        return $this->value . ' year' . ($this->value > 1 ? 's' : '');
    }
}
