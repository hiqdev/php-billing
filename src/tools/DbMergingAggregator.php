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
use hiqdev\php\billing\bill\BillRepositoryInterface;
use hiqdev\php\billing\charge\ChargeInterface;
use hiqdev\php\billing\charge\GeneralizerInterface;
use hiqdev\php\units\QuantityInterface;
use Money\Money;

/**
 * @author Andrii Vasyliev <sol@hiqdev.com>
 */
class DbMergingAggregator extends Aggregator
{
    /**
     * @var BillRepositoryInterface
     */
    protected $billRepository;

    /**
     * @var MergerInterface
     */
    protected $merger;

    public function __construct(
        GeneralizerInterface $generalizer,
        BillRepositoryInterface $billRepository,
        MergerInterface $merger
    ) {
        parent::__construct($generalizer);
        $this->billRepository = $billRepository;
        $this->merger = $merger;
    }

    /**
     * Aggregates given Charges to Bills.
     * Then merges them with Bills from DB.
     * @param ChargeInterface[]|ChargeInterface[][] $charges
     * @return BillInterface[]
     */
    public function aggregateCharges(array $charges): array
    {
        $bills  = parent::aggregateCharges($charges);
        $ids    = $this->billRepository->findIds($bills);
        $fromdb = $this->billRepository->findByIds($ids);
        $res    = $this->merger->mergeBills(array_merge($bills, $fromdb));

        return $res;
    }
}
