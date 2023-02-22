<?php
declare(strict_types=1);

namespace hiqdev\php\billing\action;
use DateInterval;
use DateTimeImmutable;

/** @readonly */
final class UsageInterval
{
    /** @readonly */
    private DateTimeImmutable $start;
    /** @readonly */
    private DateTimeImmutable $end;
    /** @readonly */
    private DateTimeImmutable $month;
    public function __construct(
        DateTimeImmutable $start,
        DateTimeImmutable $end
    ) {
        $this->start = $start;
        $this->end = $end;
        $this->month = self::toMonth($start);
    }

    private static function toMonth(DateTimeImmutable $date): DateTimeImmutable
    {
        return $date->modify('first day of this month midnight');
    }

    public static function wholeMonth(DateTimeImmutable $time)
    {
        $start = self::toMonth($time);

        return new self(
            $start,
            $start->modify('+1 month'),
        );
    }

    public static function withinMonth(
        DateTimeImmutable $month,
        DateTimeImmutable $start,
        ?DateTimeImmutable $end
    ) {
        $month = self::toMonth($month);

        $effectiveSince = max($start, $month);
        $effectiveTill = min(
            $end ?? new DateTimeImmutable('2999-01-01'),
            $month->modify('+1 month')
        );

        return new self(
            $effectiveSince,
            $effectiveTill,
        );
    }

    public function getDateTimeInterval(): DateInterval
    {
        return $this->start->diff($this->end);
    }

    public function seconds(): int
    {
        $interval = $this->getDateTimeInterval();

        return $interval->s
                + $interval->i * 60
                + $interval->h * 3600
                + $interval->d * 86400
                + $interval->m * 2592000
                + $interval->y * 31104000;
    }

    public function secondsInMonth(): int
    {
        return $this->month->format('t') * 86400;
    }

    public function ratioOfMonth(): float
    {
        $usageSeconds = $this->seconds();
        $secondsInCurrentMonth = $this->secondsInMonth();

        return $usageSeconds / $secondsInCurrentMonth;
    }
}
