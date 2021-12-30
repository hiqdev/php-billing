<?php
/**
 * PHP Billing Library
 *
 * @link      https://github.com/hiqdev/php-billing
 * @package   php-billing
 * @license   BSD-3-Clause
 * @copyright Copyright (c) 2017-2020, HiQDev (http://hiqdev.com/)
 */

namespace hiqdev\php\billing\charge\modifiers;

use DateInterval;
use DateTimeImmutable;
use hiqdev\php\billing\action\ActionInterface;
use hiqdev\php\billing\charge\ChargeInterface;
use hiqdev\php\billing\charge\derivative\ChargeDerivative;
use hiqdev\php\billing\charge\derivative\ChargeDerivativeQuery;
use hiqdev\php\billing\charge\modifiers\addons\DayPeriod;
use hiqdev\php\billing\charge\modifiers\addons\Period;
use hiqdev\php\billing\Exception\NotSupportedException;
use hiqdev\php\units\Quantity;
use hiqdev\php\units\QuantityInterface;
use Money\Money;

/**
 * Monthly cap represents a maximum number of days in a month,
 * that can be charged.
 *
 * @author Andrii Vasyliev <sol@hiqdev.com>
 */
class MonthlyCap extends Modifier
{
    private const PERIOD = 'period';

    protected ChargeDerivative $chargeDerivative;

    public function __construct(string $capDuration, array $addons = [])
    {
        parent::__construct($addons);

        $period = Period::fromString($capDuration);
        if (!$period instanceof DayPeriod) {
            throw new NotSupportedException('Only number of days in month is supported');
        }

        $this->addAddon(self::PERIOD, $period);
        $this->chargeDerivative = new ChargeDerivative();
    }

    public function getNext()
    {
        return $this;
    }

    public function modifyCharge(?ChargeInterface $charge, ActionInterface $action): array
    {
        if ($charge === null) {
            return [];
        }

        $month = $action->getTime()->modify('first day of this month midnight');
        /** @noinspection PhpUnhandledExceptionInspection */
        if (!$this->checkPeriod($month)) {
            return [$charge];
        }

        $capRatio = $this->getCapRatio($action->getTime());
        $usedDurationRation = $this->calculateEffectiveDurationRatio($action);
        if ($capRatio > $usedDurationRation) {
            $chargeQuery = new ChargeDerivativeQuery();
            $chargeQuery->changeSum($charge->getSum()->multiply($usedDurationRation));

            return [$this->chargeDerivative->__invoke($charge, $chargeQuery)];
        }

        $chargeInHoursUnderCap = $this->makeMappedCharge($charge, $action);
        return $this->splitCapFromCharge($chargeInHoursUnderCap);
    }

    private function capReached(ChargeInterface $charge, ChargeInterface $cappedCharge): bool
    {
        return $charge->getUsage()->compare($cappedCharge->getUsage()) === -1;
    }

    private function getCapInHours(): QuantityInterface
    {
        $cap = $this->getAddon(self::PERIOD);
        assert($cap instanceof DayPeriod, 'Cap can be only a DayPeriod');

        return Quantity::create('hour', $cap->getValue() * 24);
    }

    private function splitCapFromCharge(ChargeInterface $charge): array
    {
        $capRatio = $this->getCapRatio($charge->getAction()->getTime());
        $cappedUsage = $charge->getUsage()->multiply($capRatio);

        $effectiveDurationRatio = $this->calculateEffectiveDurationRatio($charge->getAction());

        $chargeQuery = new ChargeDerivativeQuery();
        $chargeQuery->changeUsage($cappedUsage);
//        $chargeQuery->changeSum($charge->getSum()->multiply());
        $newCharge = $this->chargeDerivative->__invoke($charge, $chargeQuery);

        $zeroChargeQuery = new ChargeDerivativeQuery();
        $zeroChargeQuery->changeSum(new Money(0, $charge->getSum()->getCurrency()));
        $monthDurationInHours = $this->getMonthDurationInSeconds($charge->getAction()->getTime()) / 3600;
        $zeroChargeQuery->changeUsage(Quantity::create('hour', ($effectiveDurationRatio-$capRatio) * $monthDurationInHours));
        $zeroChargeQuery->changeParent($newCharge);
        $reason = $this->getReason();
        if ($reason) {
            $zeroChargeQuery->changeComment($reason->getValue());
        }
        $newZeroCharge = $this->chargeDerivative->__invoke($charge, $zeroChargeQuery);

        return [$newCharge, $newZeroCharge];
    }

    private function getCapRatio(DateTimeImmutable $time): float
    {
        $hoursInMonth = $time->format('t') * 24;

        return ($this->getCapInHours()->getQuantity() / $hoursInMonth);
    }

    private function makeMappedCharge(ChargeInterface $charge, ActionInterface $action): ChargeInterface
    {
        $capRatio = $this->getCapRatio($action->getTime());
        $usedDurationRatio = $this->calculateEffectiveDurationRatio($action);

        $ratio = min($capRatio, $usedDurationRatio);

        $chargeQuery = new ChargeDerivativeQuery();

        $cappedUsage = $charge->getUsage()->divide($ratio);
        $cappedSum = $charge->getSum()->divide($ratio);
        if ($action->getQuantity()->getUnit()->getMeasure() === 'item') {
            $cappedUsage = $this->getCapInHours()->divide($ratio);
            $cappedSum = $charge->getSum()->divide($capRatio);
        }

        $chargeQuery->changeSum($cappedSum);
        $chargeQuery->changeUsage($cappedUsage);

        return $this->chargeDerivative->__invoke($charge, $chargeQuery);
    }

    private function getMonthDurationInSeconds(DateTimeImmutable $time): int
    {
        $month = $time->modify('first day of this month midnight');
        $nextMonth = $time->modify('first day of next month midnight');

        return $this->dateIntervalToSeconds($month->diff($nextMonth));
    }

    private function calculateEffectiveDurationRatio(ActionInterface $action): float
    {
        $month = $action->getTime()->modify('first day of this month midnight');
        $nextMonth =  $action->getTime()->modify('first day of next month midnight');
        $monthDurationInSeconds = $this->getMonthDurationInSeconds($action->getTime());

        $sale = $action->getSale();
        if ($sale === null) {
            return $this->dateIntervalToSeconds($action->getTime()->diff($nextMonth)) / $monthDurationInSeconds;
        }

        /** @var DateTimeImmutable $periodStartTime */
        $periodStartTime = max($action->getTime(), $month);
        /** @var DateTimeImmutable $periodEndTime */
        $periodEndTime = min($sale->getCloseTime() ?? new DateTimeImmutable('2199-01-01'), $nextMonth);

        $diff = $periodStartTime->diff($periodEndTime);
        $diffSeconds = $this->dateIntervalToSeconds($diff);

        return $diffSeconds / $monthDurationInSeconds;
    }

    private function dateIntervalToSeconds(DateInterval $dateInterval): int
    {
        return $dateInterval->format('%a') * 24 * 60 * 60;
    }
}
