<?php
/**
 * PHP Billing Library
 *
 * @link      https://github.com/hiqdev/php-billing
 * @package   php-billing
 * @license   BSD-3-Clause
 * @copyright Copyright (c) 2017-2020, HiQDev (http://hiqdev.com/)
 */

namespace hiqdev\php\billing\bill;

use DateTimeImmutable;
use hiqdev\billing\hiapi\action\UsageIntervalHydrator;
use hiqdev\php\billing\action\UsageInterval;

/**
 * Default bill factory.
 *
 * @author Andrii Vasyliev <sol@hiqdev.com>
 */
class BillFactory implements BillFactoryInterface
{
    public function create(BillCreationDto $dto): BillInterface
    {
        $bill = new Bill(
            $dto->id,
            $dto->type,
            $dto->time,
            $dto->sum,
            $dto->quantity,
            $dto->customer,
            $dto->target,
            $dto->plan,
            $dto->charges ?: [],
            $dto->state
        );
        if (!empty($dto->usageInterval)) {
            if ($dto->usageInterval instanceof UsageInterval) {
                $interval = $dto->usageInterval;
            } else {
                $month = $dto->usageInterval['month']['date'];
                $start = $dto->usageInterval['start']['date'];
                $end = $dto->usageInterval['end']['date'];;
                $interval = UsageInterval::withinMonth(
                    new DateTimeImmutable($month),
                    new DateTimeImmutable($start),
                    new DateTimeImmutable($end)
                );
            }
            $bill->setUsageInterval($interval);
        }
        return $bill;
    }
}
