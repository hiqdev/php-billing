<?php
declare(strict_types=1);

namespace hiqdev\php\billing\action;
use DateInterval;
use DateTimeImmutable;
use InvalidArgumentException;

/** @readonly */
final class UsageInterval
{
    /** @readonly */
    private DateTimeImmutable $start;
    /** @readonly */
    private DateTimeImmutable $end;
    /** @readonly */
    private DateTimeImmutable $month;

    private function __construct(
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

    public static function wholeMonth(DateTimeImmutable $time): self
    {
        $start = self::toMonth($time);

        return new self(
            $start,
            $start->modify('+1 month'),
        );
    }

    /**
     * Calculates the usage interval for the given month for the given start and end sale dates.
     *
     * @param DateTimeImmutable $month the month to calculate the usage interval for
     * @param DateTimeImmutable $start the start date of the sale
     * @param DateTimeImmutable|null $end the end date of the sale or null if the sale is active
     * @return static
     */
    public static function withinMonth(
        DateTimeImmutable $month,
        DateTimeImmutable $start,
        ?DateTimeImmutable $end,
        float $fractionOfMonth
    ): self {
        $month = self::toMonth($month);
        $nextMonth = $month->modify('+1 month');

        if ($end !== null && $start > $end) {
            throw new InvalidArgumentException('Start date must be less than end date');
        }

        if ($start >= $nextMonth) {
            $start = $month;
            $end = $month;
        }

        if ($end !== null && $end < $month) {
            $start = $month;
            $end = $month;
        }

        $effectiveSince = max($start, $month);

        if ($fractionOfMonth === 1.0) {
            $calcEnd = $month->modify('+1 month');
        } else {
            $startTime = strtotime(($month->format('D, d M Y H:i:s O')));
            $finishTime = strtotime($nextMonth->format('D, d M Y H:i:s O'));
            $interval = 'PT' . (($finishTime - $startTime) * $fractionOfMonth) . 'S';
            $calcEnd = $effectiveSince->add(new \DateInterval($interval));
        }
        $effectiveTill = (!empty($end) && $end < $calcEnd) ? $end : $calcEnd;

        return new self(
            $effectiveSince,
            $effectiveTill,
        );
    }

    public function start(): DateTimeImmutable
    {
        return $this->start;
    }

    public function end(): DateTimeImmutable
    {
        return $this->end;
    }

    public function dateTimeInterval(): DateInterval
    {
        return $this->start->diff($this->end);
    }

    public function seconds(): int
    {
        $interval = $this->dateTimeInterval();

        return $interval->s
                + $interval->i * 60
                + $interval->h * 3600
                + $interval->days * 86400;
    }

    public function minutes(): float
    {
        return $this->seconds() / 60;
    }

    public function hours(): float
    {
        return $this->seconds() / 60 / 60;
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
