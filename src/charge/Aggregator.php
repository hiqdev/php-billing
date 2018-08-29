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
            } elseif ($charge instanceof ChargeInterface) {
                $others = [$this->generalizer->createBill($charge)];
            } else {
                throw new \Exception('not a Charge given to Aggregator');
            }

            $bills = $this->aggregateBills($bills, $others);
        }

        return $bills;
    }

    /**
     * Aggregate arrays of bills.
     * @param BillInterface[] $bills
     * @param BillInterface[] $others
     * @return BillInterface[]
     */
    protected function aggregateBills(array $bills, array $others): array
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

    /**
     * @param BillInterface $first
     * @param BillInterface $other
     * @return BillInterface
     */
    protected function aggregateBill(BillInterface $first, BillInterface $other): BillInterface
    {
        return new Bill(
            null,
            $first->getType(),
            $first->getTime(),
            $this->aggregateSum($first, $other),
            $this->aggregateQuantity($first, $other),
            $first->getCustomer(),
            $first->getTarget(),
            $first->getPlan(),
            array_merge($first->getCharges(), $other->getCharges())
        );
    }

    protected function aggregateSum(BillInterface $first, BillInterface $other): Money
    {
        return $first->getSum()->add($other->getSum());
    }

    protected function aggregateQuantity(BillInterface $first, BillInterface $other): QuantityInterface
    {
        return $first->getQuantity()->add($other->getQuantity());
    }
}
