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
    /**
     * @var BillRepositoryInterface
     */
    protected $billRepository;

    /**
     * @var MergerInterface
     */
    protected $merger;
    /**
     * @var AggregatorInterface
     */
    private $localAggregator;

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
     * @throws \Exception
     * @return BillInterface[]
     */
    public function aggregateCharges(array $charges): array
    {
        $bills  = $this->localAggregator->aggregateCharges($charges);
        $fromdb = $this->billRepository->findByUniqueness($bills);
        $res    = $this->merger->mergeBills(array_merge($bills, $fromdb));

        return $res;
    }
}
