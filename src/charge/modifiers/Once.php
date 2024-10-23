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
    private const MONTHS_IN_YEAR = 12;

    public function per(string $interval): self
    {
        return $this->addAddon('per', $this->createInterval($interval));
    }

    private function createInterval(string $interval): Period
    {
        if ($this->isInvalidInterval($interval)) {
            throw new OnceException("Interval cannot be a fraction of a month.");
        }

        if ($this->isSupportedInterval($interval)) {
            return Period::fromString($interval);
        }

        throw new OnceException("Invalid interval. Supported: whole months or years.");
    }

    private function isInvalidInterval(string $interval): bool
    {
        // Checking for invalid fractions like '1.5 months' or non-month units like 'day'
        return (bool)preg_match('/day|0.5|1.5/', $interval);
    }

    private function isSupportedInterval(string $interval): bool
    {
        return str_contains($interval, 'month') || str_contains($interval, 'year');
    }

    public function getPer(): ?Period
    {
        return $this->getAddon('per');
    }

    public function modifyCharge(?ChargeInterface $charge, ActionInterface $action): array
    {
        if ($charge === null) {
            throw new OnceException('Charge cannot be null in Once');
        }

        $period = $this->getPer();
        if ($period === null) {
            throw new OnceException('Period cannot be null in Once');
        }

        $this->validateForOverusePricing($charge);

        // Apply the charge if applicable based on the action and interval period
        if ($this->isApplicable($action, $period)) {
            return [$charge];
        }

        // Return zero charge if the period is not applicable
        return [];
    }

    private function validateForOverusePricing(ChargeInterface $charge): void
    {
        // TODO: can I determine overuse prices form $charge->getType()?
        $chargeTypeName = $charge->getType()->getName();
        if ($chargeTypeName && str_contains($chargeTypeName, 'overuse')) {
            throw new OnceException("Overuse pricing is not compatible with once.per() billing.");
        }
    }

    public function isApplicable(ActionInterface $action, Period $period): bool
    {
        $saleTime = $this->getSaleTime($action);
        $actionTime = $action->getTime();
        $monthsDiff = $this->calculateMonthsDifference($saleTime, $actionTime);

        $intervalMonths = $this->getIntervalMonthsFromPeriod($period);

        // Check if the current period is applicable (divisible by interval)
        return $monthsDiff % $intervalMonths === 0;
    }

    private function getSaleTime(ActionInterface $action): DateTimeImmutable
    {
        $sale = $action->getSale();
        if ($sale === null || $sale->getTime() === null) {
            throw new OnceException('Sale or sale time cannot be null in Once');
        }

        return $sale->getTime();
    }

    private function calculateMonthsDifference(DateTimeImmutable $start, DateTimeImmutable $end): int
    {
        return ($end->format('Y') - $start->format('Y')) * self::MONTHS_IN_YEAR
            + ($end->format('m') - $start->format('m'));
    }

    private function getIntervalMonthsFromPeriod(Period $period): int
    {
        if ($period instanceof YearPeriod) {
            return self::MONTHS_IN_YEAR;
        } elseif ($period instanceof MonthPeriod) {
            return $period->getValue();
        }

        throw new OnceException('Unsupported interval period in Once');
    }
}
