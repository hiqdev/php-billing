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
use hiqdev\php\billing\charge\ChargeInterface;

/**
 * @author Andrii Vasyliev <sol@hiqdev.com>
 */
class DbMergingAggregator implements AggregatorInterface
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
        $dbBills = $this->billRepository->findByUniqueness($localBills);

        $filteredLocalBills = $this->excludeLocalOnlyZeroBills($localBills, $dbBills);
        $res = $this->merger->mergeBills(array_merge($filteredLocalBills, $dbBills));

        return $res;
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
