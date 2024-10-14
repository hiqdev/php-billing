<?php
/**
 * PHP Billing Library
 *
 * @link      https://github.com/hiqdev/php-billing
 * @package   php-billing
 * @license   BSD-3-Clause
 * @copyright Copyright (c) 2017-2020, HiQDev (http://hiqdev.com/)
 */

namespace hiqdev\php\billing\tools;

use hiqdev\php\billing\bill\BillInterface;
use hiqdev\php\billing\bill\BillRepositoryInterface;
use hiqdev\php\billing\charge\Charge;
use hiqdev\php\billing\charge\ChargeInterface;

/**
 * @author Andrii Vasyliev <sol@hiqdev.com>
 */
class DbMergingAggregator implements AggregatorInterface, MergerInterface
{
    protected BillRepositoryInterface $billRepository;
    protected MergerInterface $merger;
    private AggregatorInterface $localAggregator;

    public function __construct(
        AggregatorInterface $localAggregator,
        BillRepositoryInterface $billRepository,
        MergerInterface $merger
    ) {
        $this->billRepository = $billRepository;
        $this->merger = $merger;
        $this->localAggregator = $localAggregator;
    }

    public function mergeBills(array $bills): array
    {
        $dbBills = $this->billRepository->findByUniqueness($bills);
        $filteredLocalBills = $this->excludeLocalOnlyZeroBills($bills, $dbBills);

        return $this->merger->mergeBills(array_merge($filteredLocalBills, $dbBills));
    }

    public function mergeBill(BillInterface $first, BillInterface $other): BillInterface
    {
        return $this->merger->mergeBill($first, $other);
    }

    /**
     * Aggregates given Charges to Bills.
     * Then merges them with Bills from DB.
     *
     * @param ChargeInterface[]|ChargeInterface[][] $charges
     * @return BillInterface[]
     * @throws \Exception
     */
    public function aggregateCharges(array $charges): array
    {
        $localBills = $this->localAggregator->aggregateCharges($charges);
        $bills = $this->mergeBills($localBills);

        return $bills;
    }

    /**
     * When a new Zero bill is being produced, it should not be persisted
     * unless there is already a bill of this uniqueness in the DBMS.
     *
     * @param BillInterface[] $localBills
     * @param BillInterface[] $dbBills
     * @return BillInterface[]
     */
    private function excludeLocalOnlyZeroBills(array $localBills, array $dbBills): array
    {
        foreach ($localBills as $i => $localBill) {
            foreach ($localBill->getCharges() as $charge) {
                /** @var Charge $charge */
                if ($charge->hasEvents()) {
                    continue 2;
                }
            }
            $isZeroSum = $localBill->getSum()->getAmount() === "0";
            if (!$isZeroSum) {
                continue;
            }

            $localUniqueString = $localBill->getUniqueString();
            foreach ($dbBills as $dbBill) {
                if ($dbBill->getUniqueString() === $localUniqueString) {
                    continue 2;
                }
            }

            unset($localBills[$i]);
        }

        return $localBills;
    }

}
