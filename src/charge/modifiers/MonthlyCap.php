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

        $chargeInHoursUnderCap = $this->makeMappedCharge($charge, $action);
        if ($this->capReached($chargeInHoursUnderCap)) {
            return $this->splitCapFromCharge($chargeInHoursUnderCap);
        }

        return [$chargeInHoursUnderCap];
    }

    private function capReached(ChargeInterface $charge): bool
    {
        return $charge->getUsage()->compare($this->getCapInHours()) === 1; // Usage is greater than a cap
    }

    private function getCapInHours(): QuantityInterface
    {
        $cap = $this->getAddon(self::PERIOD);
        assert($cap instanceof DayPeriod, 'Cap can be only a DayPeriod');

        return Quantity::create('hour', $cap->getValue() * 24);
    }

    private function splitCapFromCharge(ChargeInterface $charge): array
    {
        $cappedHours = $this->getCapInHours();
        $diff = 1-($charge->getUsage()->subtract($cappedHours)->getQuantity()/$charge->getUsage()->getQuantity());

        $chargeQuery = new ChargeDerivativeQuery();
        $chargeQuery->changeUsage($cappedHours);
        $chargeQuery->changeSum($charge->getSum()->multiply($diff));
        $newCharge = $this->chargeDerivative->__invoke($charge, $chargeQuery);

        $zeroChargeQuery = new ChargeDerivativeQuery();
        $zeroChargeQuery->changeSum(new Money(0, $charge->getSum()->getCurrency()));
        $zeroChargeQuery->changeUsage($charge->getUsage()->subtract($cappedHours));
        $zeroChargeQuery->changeParent($newCharge);
        $reason = $this->getReason();
        if ($reason) {
            $zeroChargeQuery->changeComment($reason->getValue());
        }
        $newZeroCharge = $this->chargeDerivative->__invoke($charge, $zeroChargeQuery);

        return [$newCharge, $newZeroCharge];
    }

    private function getEffectiveCoefficient(ActionInterface $action): float
    {
        $hoursInMonth = $action->getTime()->format('t') * 24;

        return 1 / ($this->getCapInHours()->getQuantity() / $hoursInMonth);
    }

    private function makeMappedCharge(ChargeInterface $charge, ActionInterface $action): ChargeInterface
    {
        $coefficient = $this->getEffectiveCoefficient($action);
        $quantityUnderCap = $charge->getUsage()->multiply($coefficient);

        $chargeQuery = new ChargeDerivativeQuery();
        $chargeQuery->changeUsage(
            $this->getCapInHours()->multiply($quantityUnderCap->getQuantity())
        );
        $chargeQuery->changeSum($charge->getSum()->multiply($coefficient));

        return $this->chargeDerivative->__invoke($charge, $chargeQuery);
    }
}
