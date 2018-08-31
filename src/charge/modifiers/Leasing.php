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

use hiqdev\php\billing\action\ActionInterface;
use hiqdev\php\billing\charge\ChargeInterface;
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
        return new Type(Type::ANY, 'discount,leasing');
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

        $month = $action->getTime()->modify('first day of this month midnight');
        if (!$this->checkPeriod($month)) {
            return [];
        }

        $reason = $this->getReason();
        if ($reason) {
            $charge->setComment($reason->getValue());
        }

        return [$charge];
    }

    protected function ensureIsValid()
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
}
