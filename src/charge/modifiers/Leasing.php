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
use hiqdev\php\billing\charge\Charge;
use hiqdev\php\billing\charge\ChargeInterface;
use hiqdev\php\billing\charge\modifiers\event\LeasingWasFinished;
use hiqdev\php\billing\formula\FormulaSemanticsError;
use hiqdev\php\billing\price\SinglePrice;
use hiqdev\php\billing\target\Target;
use hiqdev\php\billing\type\Type;
use hiqdev\php\units\Quantity;
use Money\Money;

/**
 * Leasing.
 *
 * @author Andrii Vasyliev <sol@hiqdev.com>
 */
class Leasing extends Modifier
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
        return new Type(Type::ANY, 'monthly,leasing');
    }

    public function getTarget()
    {
        return new Target(Target::ANY, Target::ANY);
    }

    public function till($dummy)
    {
        throw new FormulaSemanticsError('till can not be defined for leasing');
    }

    public function modifyCharge(?ChargeInterface $charge, ActionInterface $action): array
    {
        if ($charge === null) {
            throw new \Exception('unexpected null charge in Leasing, to be implemented');
        }

        $this->ensureIsValid();

        $reason = $this->getReason();
        if ($reason) {
            $charge->setComment($reason->getValue());
        }

        $month = $action->getTime()->modify('first day of this month midnight');
        if (!$this->checkPeriod($month)) {
            if ($this->isFirstMonthAfterLeasingPassed($month)) {
                return [$this->createLeasingFinishingCharge($charge, $month)];
            }

            return [];
        }

        return [$this->createLeasingCharge($charge, $month)];
    }

    protected function ensureIsValid(): void
    {
        $since = $this->getSince();
        if ($since === null) {
            throw new FormulaSemanticsError('no since given for leasing');
        }

        $term = $this->getTerm();
        if ($term === null) {
            throw new FormulaSemanticsError('no term given for leasing');
        }
    }

    private function isFirstMonthAfterLeasingPassed(\DateTimeImmutable $time): bool
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

    private function createLeasingFinishingCharge(ChargeInterface $charge, \DateTimeImmutable $month): ChargeInterface
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
        $result->recordThat(LeasingWasFinished::onCharge($result, $month));
        if ($charge->getComment()) {
            $result->setComment($charge->getComment());
        }

        return $result;
    }

    private function createLeasingCharge(ChargeInterface $charge, \DateTimeImmutable $month): ChargeInterface
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

        return $result;
    }
}
