<?php
/**
 * PHP Billing Library
 *
 * @link      https://github.com/hiqdev/php-billing
 * @package   php-billing
 * @license   BSD-3-Clause
 * @copyright Copyright (c) 2017-2018, HiQDev (http://hiqdev.com/)
 */

namespace hiqdev\php\billing\tools;

use hiqdev\php\billing\bill\Bill;
use hiqdev\php\billing\bill\BillInterface;
use hiqdev\php\billing\charge\AggregationException;
use hiqdev\php\billing\charge\ChargeInterface;
use hiqdev\php\units\QuantityInterface;
use Money\Money;

/**
 * @author Andrii Vasyliev <sol@hiqdev.com>
 */
class Merger implements MergerInterface
{
    /**
     * {@inheritdoc}
     */
    public function mergeBills(array $bills): array
    {
        $res = [];
        foreach ($bills as $bill) {
            $uid = $bill->getUniqueString();
            if (empty($res[$uid])) {
                $res[$uid] = $bill;
            } else {
                $res[$uid] = $this->mergeBill($res[$uid], $bill);
            }
        }

        return $res;
    }

    /**
     * {@inheritdoc}
     */
    public function mergeBill(BillInterface $first, BillInterface $other): BillInterface
    {
        $charges = $this->mergeCharges(array_merge($first->getCharges(), $other->getCharges()));

        return new Bill(
            $this->mergeId($first, $other),
            $first->getType(),
            $first->getTime(),
            $this->mergeSum($first, $other, $charges),
            $this->mergeQuantity($first, $other, $charges),
            $first->getCustomer(),
            $first->getTarget(),
            $first->getPlan(),
            $charges
        );
    }

    /**
     * Merge
     * @param ChargeInterface[] $charges
     * @return ChargeInterface[]
     */
    protected function mergeCharges(array $charges): array
    {
        $res = [];
        foreach ($charges as $charge) {
            $uid = $charge->getUniqueString();
            if (empty($res[$uid])) {
                $res[$uid] = $charge;
            } else {
                $res[$uid] = $this->mergeCharge($res[$uid], $charge);
            }
        }

        return $res;
    }

    /**
     * Merges two charges.
     * Simple implementation just returns latest charge.
     * @param ChargeInterface $first
     * @param ChargeInterface $other
     * @return ChargeInterface
     */
    protected function mergeCharge(ChargeInterface $first, ChargeInterface $other): ChargeInterface
    {
        if (!$first->hasId()) {
            $first->setId($other->getId());
        }

        return $first;
    }

    /**
     * @param BillInterface $first
     * @param BillInterface $other
     * @return string|int|null
     */
    protected function mergeId(BillInterface $first, BillInterface $other)
    {
        if ($first->getId() === null) {
            return $other->getId();
        }
        if ($other->getId() === null) {
            return $first->getId();
        }
        if ($first->getId() === $other->getId()) {
            return $other->getId();
        }

        throw new AggregationException('cannot merge bills with different IDs');
    }

    /**
     * @param BillInterface $first
     * @param BillInterface $other
     * @param ChargeInterface[] $charges
     * @return Money
     */
    protected function mergeSum(BillInterface $first, BillInterface $other, array $charges): Money
    {
        if (empty($charges)) {
            return $first->getSum()->add($other->getSum());
        }

        $sum = array_shift($charges)->getSum();
        foreach ($charges as $charge) {
            $sum = $sum->add($charge->getSum());
        }

        return $sum->negative();
    }

    /**
     * @param BillInterface $first
     * @param BillInterface $other
     * @param ChargeInterface[] $charges
     * @return QuantityInterface
     */
    protected function mergeQuantity(BillInterface $first, BillInterface $other, array $charges): QuantityInterface
    {
        if (empty($charges)) {
            return $first->getQuantity()->add($other->getQuantity());
        }

        $usage = array_shift($charges)->getUsage();
        foreach ($charges as $charge) {
            if (! $charge->getUsage()->isConvertible($usage->getUnit())) {
                continue;
            }
            $usage = $usage->add($charge->getUsage());
        }

        return $usage;
    }
}
