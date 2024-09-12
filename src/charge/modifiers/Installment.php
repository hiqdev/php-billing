<?php

declare(strict_types=1);
/**
 * PHP Billing Library
 *
 * @link      https://github.com/hiqdev/php-billing
 * @package   php-billing
 * @license   BSD-3-Clause
 * @copyright Copyright (c) 2017-2020, HiQDev (http://hiqdev.com/)
 */

namespace hiqdev\php\billing\charge\modifiers;

use DateTimeImmutable;
use hiqdev\php\billing\action\ActionInterface;
use hiqdev\php\billing\charge\Charge;
use hiqdev\php\billing\charge\ChargeInterface;
use hiqdev\php\billing\charge\modifiers\addons\Period;
use hiqdev\php\billing\charge\modifiers\event\InstallmentWasFinished;
use hiqdev\php\billing\charge\modifiers\event\InstallmentWasStarted;
use hiqdev\php\billing\formula\FormulaSemanticsError;
use hiqdev\php\billing\price\SinglePrice;
use hiqdev\php\billing\target\Target;
use hiqdev\php\billing\type\AnyIdType;
use hiqdev\php\units\Quantity;
use Money\Money;

/**
 * Installment.
 *
 * @author Andrii Vasyliev <sol@hiqdev.com>
 */
class Installment extends Modifier
{
    public function buildPrice(Money $sum)
    {
        $type = $this->getType();
        $target = $this->getTarget();
        $prepaid = Quantity::create('items', 0);

        return new SinglePrice(null, $type, $target, null, $prepaid, $sum);
    }

    public function getType()
    {
        $since = $this->getSince();
        if ($since->getValue() < new DateTimeImmutable('2024-01-01')) {
            return new AnyIdType('monthly,leasing');
        }
        return new AnyIdType('monthly,installment');
    }

    public function getTarget()
    {
        return new Target(Target::ANY, Target::ANY);
    }

    public function till($dummy)
    {
        throw new FormulaSemanticsError('till can not be defined for installment');
    }

    public function modifyCharge(?ChargeInterface $charge, ActionInterface $action): array
    {
        if ($charge === null) {
            throw new \Exception('unexpected null charge in Installment, to be implemented');
        }

        $this->ensureIsValid();

        $reason = $this->getReason();
        if ($reason) {
            $charge->setComment($reason->getValue());
        }

        $month = $action->getTime()->modify('first day of this month midnight');
        if (!$this->checkPeriod($month)) {
            if ($this->isFirstMonthAfterInstallmentPassed($month)) {
                return [$this->createInstallmentFinishingCharge($charge, $month)];
            }

            return [];
        }

        return [$this->createInstallmentCharge($charge, $month)];
    }

    protected function ensureIsValid(): void
    {
        $since = $this->getSince();
        if ($since === null) {
            throw new FormulaSemanticsError('no since given for installment');
        }

        $term = $this->getTerm();
        if ($term === null) {
            throw new FormulaSemanticsError('no term given for installment');
        }
    }

    private function isFirstMonthInInstallmentPassed(DateTimeImmutable $time): bool
    {
        $since = $this->getSince();
        if ($since && $since->getValue() > $time) {
            return false;
        }

        if ($since->getValue()->diff($time)->format('%a') === '0') {
            return true;
        }

        return false;
    }

    private function isFirstMonthAfterInstallmentPassed(DateTimeImmutable $time): bool
    {
        $since = $this->getSince();
        if ($since && $since->getValue() > $time) {
            return false;
        }

        $till = $this->getTill();
        if ($till && $till->getValue() <= $time) {
            if ($till->getValue()->diff($time)->format('%a') === '0') {
                return true;
            }
        }

        $term = $this->getTerm();
        if ($term && $term->addTo($since->getValue())->diff($time)->format('%a') === '0') {
            return true;
        }

        return false;
    }

    private function createInstallmentFinishingCharge(ChargeInterface $charge, DateTimeImmutable $month): ChargeInterface
    {
        $result = new Charge(
            null,
            $this->getType(),
            $charge->getTarget(),
            $charge->getAction(),
            $charge->getPrice(),
            $charge->getUsage(),
            new Money(0, $charge->getSum()->getCurrency())
        );
        $result->recordThat(InstallmentWasFinished::onCharge($result, $month));
        if ($charge->getComment()) {
            $result->setComment($charge->getComment());
        }

        return $result;
    }

    private function createInstallmentStartingCharge(ChargeInterface $charge, DateTimeImmutable $month): ChargeInterface
    {
        $charge->recordThat(InstallmentWasStarted::onCharge($charge, $month));

        return $charge;
    }

    private function createInstallmentCharge(ChargeInterface $charge, DateTimeImmutable $month): ChargeInterface
    {
        $result = new Charge(
            null,
            $this->getType(),
            $charge->getTarget(),
            $charge->getAction(),
            $charge->getPrice(),
            $charge->getUsage(),
            $charge->getSum()
        );

        if ($charge->getComment()) {
            $result->setComment($charge->getComment());
        }

        if ($this->isFirstMonthInInstallmentPassed($month)) {
            return $this->createInstallmentStartingCharge($result, $month);
        }

        return $result;
    }

    public function getRemainingPeriods(DateTimeImmutable $currentDate): ?Period
    {
        $since = $this->getSince();
        $term = $this->getTerm();

        if ($since === null || $term === null) {
            return null;
        }

        $className = get_class($term);
        $passedRatio = $term->countPeriodsPassed($since->getValue(), $currentDate);

        return new $className(
            $term->getValue() - ($passedRatio * $term->getValue())
        );
    }
}
