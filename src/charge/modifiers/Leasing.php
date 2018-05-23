<?php
/**
 * PHP Billing Library
 *
 * @link      https://github.com/hiqdev/php-billing
 * @package   php-billing
 * @license   BSD-3-Clause
 * @copyright Copyright (c) 2017-2018, HiQDev (http://hiqdev.com/)
 */

namespace hiqdev\php\billing\charge\modifiers;

use DateTimeImmutable;
use hiqdev\php\billing\action\ActionInterface;
use hiqdev\php\billing\charge\ChargeInterface;
use hiqdev\php\billing\charge\modifiers\addons\Period;
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
    const TERM = 'term';

    public function lasts($term): self
    {
        return $this->addAddon(self::TERM, Period::fromString($term));
    }

    public function getTerm(): ?Period
    {
        return $this->getAddon(self::TERM);
    }

    public function isAbsolute()
    {
        return $this->getAddon(self::VALUE)->isAbsolute();
    }

    public function isRelative()
    {
        return !$this->isAbsolute();
    }

    public function calculateSum(ChargeInterface $charge = null): Money
    {
        return $this->getValue($charge)->calculateSum($charge);
    }

    public function buildPrice(Money $sum)
    {
        $type = $this->getType();
        $target = $this->getTarget();
        $prepaid = Quantity::items(0);

        return new SinglePrice(null, $type, $target, null, $prepaid, $sum);
    }

    public function getType()
    {
        return new Type(Type::ANY, 'discount');
    }

    public function getTarget()
    {
        return new Target(Target::ANY, Target::ANY);
    }

    public function till($dummy)
    {
        throw new \Exception('till can not be defined for leasing');
    }

    public function modifyCharge(?ChargeInterface $charge, ActionInterface $action): array
    {
        if ($charge === null) {
            $charge = $this->calculateCharge($charge, $action);
        }

        $month = $action->getTime()->modify('first day of this month midnight');
        $num = $this->countPeriodsPassed($month);
        if ($num >= 1 || !$this->checkPeriod($month)) {
            return [];
        }

        $reason = $this->getReason();
        if ($reason) {
            $charge->setComment($reason->getValue());
        }

        return [$charge];
    }

    protected function countPeriodsPassed(DateTimeImmutable $time)
    {
        $since = $this->getSince();
        if ($since === null) {
            throw new \Exception('no since given for leasing');
        }

        $term = $this->getTerm();
        if ($term === null) {
            throw new \Exception('no term given for leasing');
        }

        return $term->countPeriodsPassed($since->getValue(), $time);
    }
}
