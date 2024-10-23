<?php declare(strict_types=1);

namespace hiqdev\php\billing\charge\modifiers;

use DateTimeImmutable;
use hiqdev\php\billing\action\ActionInterface;
use hiqdev\php\billing\charge\ChargeInterface;
use hiqdev\php\billing\charge\modifiers\addons\MonthPeriod;
use hiqdev\php\billing\charge\modifiers\addons\Period;
use hiqdev\php\billing\charge\modifiers\addons\YearPeriod;
use hiqdev\php\billing\charge\modifiers\exception\OnceException;

/**
 * 1. API:
 * - once.per('1 year') – bill every month that matches the month of sale of the object
 * - once.per('3 months') – bill every third month, starting from the month of sale of the object
 * - once.per('day'), once.per('1.5 months') – throws an interpret-time exception, a value must NOT be a fraction of the month
 * 2. In months where the formula should NOT bill, it should produce a ZERO charge.
 * 3. If the sale is re-opened, the formula starts over.
 * 4. It should throw exception for overuse prices.
 */
class Once extends Modifier
{
    public function per(string $interval): self
    {
        return $this->addAddon('per', $this->createInterval($interval));
    }

    private function createInterval(string $interval): Period
    {
        // once.per('day'), once.per('1.5 months') – throws an interpret-time exception, a value must NOT be a fraction of the month
        // Check for invalid intervals like 'day', '1.5 months'
        if (preg_match('/day|0.5|1.5/', $interval)) {
            throw new OnceException("Interval cannot be a fraction of a month.");
        }

        if (strpos($interval, 'month') !== false || strpos($interval, 'year') !== false) {
            return Period::fromString($interval);
        }

        throw new OnceException("Invalid interval. Supported: whole months or years.");
    }

    public function getPer(): ?Period
    {
        return $this->getAddon('per');
    }

    public function modifyCharge(?ChargeInterface $charge, ActionInterface $action): array
    {
        if ($charge === null) {
            throw new OnceException('unexpected null charge in Once, to be implemented');
        }

        $period = $this->getPer();

        if ($period === null) {
            throw new OnceException('Unexpected null period in Once');
        }

        // TODO: can I determine overuse prices form $charge->getType()?
        $chargeTypeName = $charge->getType()->getName();
        if ($chargeTypeName && str_contains($chargeTypeName, 'overuse')) {
            throw new \Exception('Unexpected null since in Once');
        }

        if ($this->isApplicable($action, $period)) {
            // If it's a billing period, return the normal charge
            // TODO: return passed charge?
            return [$charge];
        }

        // If it's not a billing period, return a zero charge
        return [];
    }

    public function validateForOverusePricing(bool $isOverusePricing)
    {
        if ($isOverusePricing) {
            throw new OnceException("Overuse pricing is not compatible with once.per() billing.");
        }
    }

    public function isApplicable(ActionInterface $action, Period $period): bool
    {
        $saleTime = $action->getSale()?->getTime();

        if ($saleTime === null) {
            throw new OnceException('Unexpected null sale time in Once');
        }

        $actionTime = $action->getTime();
        $monthsDiff = $this->monthsDiff($saleTime, $actionTime);

        $intervalMonths = $this->getIntervalMonthsFromPeriod($period);

        // Check if it's a billing period based on the interval
        // In the same day with interval year or month or three months
        return $monthsDiff % $intervalMonths === 0;
    }

    private function monthsDiff(DateTimeImmutable $start, DateTimeImmutable $end): int
    {
        return ($end->format('Y') - $start->format('Y'))
            * 12 + ($end->format('m') - $start->format('m'));
    }

    private function getIntervalMonthsFromPeriod(Period $period): int
    {
        if ($period instanceof YearPeriod) {
            // TODO: is it possible to avoid hardcoding 12?
            return 12;
        } elseif ($period instanceof MonthPeriod) {
            return $period->getValue();
        }

        throw new OnceException(sprintf('Invalid %s period in Once', $period::class));
    }
}
