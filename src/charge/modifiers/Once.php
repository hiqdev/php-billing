<?php declare(strict_types=1);

namespace hiqdev\php\billing\charge\modifiers;

use DateTimeImmutable;
use hiapi\legacy\lib\deps\dbc;
use hiqdev\php\billing\action\ActionInterface;
use hiqdev\php\billing\charge\ChargeInterface;
use hiqdev\php\billing\charge\derivative\ChargeDerivative;
use hiqdev\php\billing\charge\derivative\ChargeDerivativeQuery;
use hiqdev\php\billing\charge\modifiers\addons\MonthPeriod;
use hiqdev\php\billing\charge\modifiers\addons\Period;
use hiqdev\php\billing\charge\modifiers\addons\YearPeriod;
use hiqdev\php\billing\formula\FormulaEngineException;
use Money\Money;

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
    private const MONTHS_IN_YEAR_ON_EARTH = 12;

    protected ChargeDerivative $chargeDerivative;

    public function __construct(array $addons = [])
    {
        parent::__construct($addons);

        $this->chargeDerivative = new ChargeDerivative();
    }

    public function per(string $interval): self
    {
        return $this->addAddon('per', $this->createPeriod($interval));
    }

    private function createPeriod(string $interval): Period
    {
        $period = Period::fromString($interval);

        if ($this->isSupportedPeriod($period)) {
            return $period;
        }

        throw new FormulaEngineException("Invalid interval. Supported: whole months or years.");
    }


    private function isSupportedPeriod(Period $period): bool
    {
        return $period instanceof MonthPeriod || $period instanceof YearPeriod;
    }

    public function getPer(): ?Period
    {
        return $this->getAddon('per');
    }

    public function modifyCharge(?ChargeInterface $charge, ActionInterface $action): array
    {
        $period = $this->getPer();

        $this->assertPeriod($period);
        $this->assertCharge($charge);

        // Apply the charge if applicable based on the action and interval period
        if ($this->isApplicable($action, $period)) {
            return [$charge];
        }

        // Return zero charge if the period is not applicable
        return [$this->createNewZeroCharge($charge)];
    }

    private function createNewZeroCharge(ChargeInterface $charge): ChargeInterface
    {
        $zeroChargeQuery = new ChargeDerivativeQuery();
        $zeroChargeQuery->changeSum(new Money(0, $charge->getSum()->getCurrency()));
        $reason = $this->getReason();
        if ($reason) {
            $zeroChargeQuery->changeComment($reason->getValue());
        }

        return $this->chargeDerivative->__invoke($charge, $zeroChargeQuery);
    }

    private function assertPeriod(?Period $period)
    {
        if ($period === null) {
            throw new FormulaEngineException('Period cannot be null in Once');
        }
    }

    private function assertCharge(?ChargeInterface $charge)
    {
        if ($charge === null) {
            throw new FormulaEngineException('Charge cannot be null in Once');
        }
    }

    private function isApplicable(ActionInterface $action, Period $period): bool
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
            throw new FormulaEngineException('Sale or sale time cannot be null in Once');
        }

        return $sale->getTime();
    }

    private function calculateMonthsDifference(DateTimeImmutable $start, DateTimeImmutable $end): int
    {
        $interval = $end->diff($start);
        return ($interval->y * self::MONTHS_IN_YEAR_ON_EARTH) + $interval->m;
    }

    private function getIntervalMonthsFromPeriod(Period $period): int
    {
        if ($period instanceof YearPeriod) {
            return self::MONTHS_IN_YEAR_ON_EARTH;
        } elseif ($period instanceof MonthPeriod) {
            return $period->getValue();
        }

        throw new FormulaEngineException('Unsupported interval period in Once');
    }
}
