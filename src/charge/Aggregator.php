<?php
/**
 * PHP Billing Library
 *
 * @link      https://github.com/hiqdev/php-billing
 * @package   php-billing
 * @license   BSD-3-Clause
 * @copyright Copyright (c) 2017, HiQDev (http://hiqdev.com/)
 */

namespace hiqdev\php\billing\charge;

use DateTime;
use hiqdev\php\billing\bill\Bill;
use hiqdev\php\billing\bill\BillInterface;
use hiqdev\php\units\QuantityInterface;
use Money\Money;

/**
 * @author Andrii Vasyliev <sol@hiqdev.com>
 */
class Aggregator implements AggregatorInterface
{
    /**
     * @var BillInterface[]
     */
    protected $bills = [];

    public function aggregateCharges(array $charges)
    {
        $bills = [];
        foreach ($charges as $charge) {
            if (is_array($charge)) {
                $others = $this->aggregateCharges($charge);
                $bills = $this->aggregateBills($bills, $others);
            } else if ($charge instanceof ChargeInterface) {
                $bill = $this->createBill($charge);
                $bills = $this->aggregateBills($bills, [$bill]);
            } else {
                throw new \Exception('not a Charge given to Aggregator');
            }
        }

        return $bills;
    }

    /**
     * Aggregate arrays of bills.
     * @param BillInterface[] $bills
     * @param BillInterface[] $others
     * @return BillInterface[]
     */
    public function aggregateBills(array $bills, array $others)
    {
        foreach ($others as $bill) {
            $uid = $bill->getUniqueId();
            if (empty($bills[$uid])) {
                $bills[$uid] = $bill;
            } else {
                $bills[$uid] = $this->aggregateBill($bills[$uid], $bill);
            }
        }

        return $bills;
    }

    public function aggregateBill(BillInterface $first, BillInterface $other)
    {
        return new Bill(
            null,
            $first->getType(),
            $first->getTime(),
            $this->aggregateSum($first->getSum(), $other->getSum()),
            $this->aggregateQuantity($first->getQuantity(), $other->getQuantity()),
            $first->getCustomer(),
            $first->getTarget(),
            $first->getPlan(),
            array_merge($first->getCharges(), $other->getCharges())
        );
    }

    public function aggregateSum(Money $first, Money $other)
    {
        return $first->add($other);
    }

    public function aggregateQuantity(QuantityInterface $first, QuantityInterface $other)
    {
        return $first->add($other);
    }

    public function createBill(ChargeInterface $charge)
    {
        return new Bill(
            null,
            $this->generalizeType($charge),
            $this->generalizeTime($charge),
            $this->generalizeSum($charge),
            $this->generalizeQuantity($charge),
            $this->generalizeCustomer($charge),
            $this->generalizeTarget($charge),
            $this->generalizePlan($charge),
            [$charge]
        );
    }

    public function generalizeType(ChargeInterface $charge)
    {
        return $charge->getPrice()->getType();
    }

    public function generalizeTime(ChargeInterface $charge)
    {
        $date = new DateTime($charge->getTime());

        return $date->modify('first day of this month midnight');
    }

    public function generalizeSum(ChargeInterface $charge)
    {
        return $charge->getSum();
    }

    public function generalizeQuantity(ChargeInterface $charge)
    {
        return $charge->getUsage();
    }

    public function generalizeCustomer(ChargeInterface $charge)
    {
        return $charge->getAction()->getCustomer();
    }

    public function generalizeTarget(ChargeInterface $charge)
    {
        return $charge->getPrice()->getTarget();
    }

    public function generalizePlan(ChargeInterface $charge)
    {
        return $charge->getPrice()->getPlan();
    }
}
