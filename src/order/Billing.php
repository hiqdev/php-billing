<?php
/**
 * PHP Billing Library
 *
 * @link      https://github.com/hiqdev/php-billing
 * @package   php-billing
 * @license   BSD-3-Clause
 * @copyright Copyright (c) 2017-2018, HiQDev (http://hiqdev.com/)
 */

namespace hiqdev\php\billing\order;

use hiqdev\php\billing\bill\BillRepositoryInterface;
use hiqdev\php\billing\tools\AggregatorInterface;
use hiqdev\php\billing\tools\MergerInterface;
use hiqdev\php\billing\tools\DbMergingAggregator;

/**
 * Billing calculates and saves bills for given order.
 *
 * @author Andrii Vasyliev <sol@hiqdev.com>
 */
class Billing implements BillingInterface
{
    /**
     * @var CalculatorInterface
     */
    protected $calculator;
    /**
     * @var AggregatorInterface
     */
    protected $aggregator;
    /**
     * @var AggregatorInterface
     */
    protected $repoAggregator;
    /**
     * @var MergerInterface
     */
    protected $merger;
    /**
     * @var BillRepositoryInterface
     */
    protected $repository;

    public function __construct(
        CalculatorInterface $calculator,
        AggregatorInterface $aggregator,
        MergerInterface $merger,
        ?BillRepositoryInterface $repository
    ) {
        $this->calculator = $calculator;
        $this->aggregator = $aggregator;
        $this->merger = $merger;
        $this->repository = $repository;
    }

    public function calculate(OrderInterface $order): array
    {
        $charges = $this->calculator->calculateOrder($order);
        $bills = $this->aggregator->aggregateCharges($charges);

        return $this->merger->mergeBills($bills);
    }

    public function perform(OrderInterface $order): array
    {
        $charges = $this->calculator->calculateOrder($order);
        $bills = $this->getRepoAggregator()->aggregateCharges($charges);

        return $this->saveBills($bills);
    }

    private function getRepoAggregator(): AggregatorInterface
    {
        if ($this->repoAggregator === null) {
            $this->repoAggregator = new DbMergingAggregator($this->aggregator, $this->repository, $this->merger);
        }

        return $this->repoAggregator;
    }

    /**
     * @param BillInterface[] $bills
     * @return BillInterface[]
     */
    private function saveBills(array $bills): array
    {
        $res = [];
        foreach ($bills as $key => $bill) {
            $res[$key] = $this->repository->save($bill);
        }

        return $res;
    }
}
