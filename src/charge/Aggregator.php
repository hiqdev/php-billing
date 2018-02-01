<?php
/**
 * PHP Billing Library
 *
 * @link      https://github.com/hiqdev/php-billing
 * @package   php-billing
 * @license   BSD-3-Clause
 * @copyright Copyright (c) 2017-2018, HiQDev (http://hiqdev.com/)
 */

namespace hiqdev\php\billing\charge;

use hiqdev\php\billing\bill\Bill;
use hiqdev\php\billing\bill\BillInterface;
use hiqdev\php\units\QuantityInterface;
use Money\Money;

/**
 * @author Andrii Vasyliev <sol@hiqdev.com>
 * TODO: Split out generalizations to Generalizer class.
 */
class Aggregator implements AggregatorInterface
{
    /**
     * @var BillInterface[]
     */
    protected $bills = [];

    /**
     * @var GeneralizerInterface
     */
    protected $generalizer;

    public function __construct(GeneralizerInterface $generalizer)
    {
        $this->generalizer = $generalizer;
    }

    public function aggregateCharges(array $charges)
    {
        $bills = [];
        foreach ($charges as $charge) {
            if (is_array($charge)) {
                $others = $this->aggregateCharges($charge);
                $bills = $this->aggregateBills($bills, $others);
            } elseif ($charge instanceof ChargeInterface) {
                $bill = $this->generalizer->createBill($charge);
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
            $uid = $bill->getUniqueString();
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
}
