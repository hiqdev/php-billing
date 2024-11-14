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
     * @throws InvalidArgumentException if the start date is greater than the end date
     * @return static
     */
    public static function withinMonth(
        DateTimeImmutable $month,
        DateTimeImmutable $start,
        ?DateTimeImmutable $end
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
        $effectiveTill = min(
            $end ?? new DateTimeImmutable('2999-01-01'),
            $month->modify('+1 month')
        );

        return new self(
            $effectiveSince,
            $effectiveTill,
        );
    }

    /**
     * Calculates the usage interval for the given month for the given start date and fraction of month value.
     *
     * @param DateTimeImmutable $month the month to calculate the usage interval for
     * @param DateTimeImmutable $start the start date of the sale
     * @param float $fractionOfMonth the fraction of manth
     * @return static
     */
    public static function withMonthAndFraction(
        DateTimeImmutable $month,
        DateTimeImmutable $start,
        float $fractionOfMonth
    ): self {
        if ($fractionOfMonth < 0 || $fractionOfMonth > 1) {
            throw new InvalidArgumentException('Fraction of month must be between 0 and 1');
        }
        $month = self::toMonth($month);
        $nextMonth = $month->modify('+1 month');

        if ($start >= $nextMonth) {
            $start = $month;
        }

        $effectiveSince = max($start, $month);

        if ($fractionOfMonth === 1.0) {
            $effectiveTill = $month->modify('+1 month');
        } else {
            $interval = new self($month, $nextMonth);
            $seconds = $interval->secondsInMonth() * $fractionOfMonth;
            $effectiveTill = $effectiveSince->modify(sprintf('+%d seconds', $seconds));
        }

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

    /**
     * Extends the usage interval to include both current and other intervals.
     *
     * @param UsageInterval $other
     * @return self
     */
    public function extend(self $other): self
    {
        $newStart = min($this->start, $other->start);
        $newEnd = max($this->end, $other->end);

        if ($newStart > $newEnd) {
            throw new InvalidArgumentException('Cannot extend intervals: resulting interval would be invalid');
        }
        return new self(
            $newStart,
            $newEnd,
        );
    }
}
