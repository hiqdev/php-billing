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
    /**
     * @var CollectorInterface
     */
    protected $collector;

    public function __construct(
        CalculatorInterface $calculator,
        AggregatorInterface $aggregator,
        MergerInterface $merger,
        ?BillRepositoryInterface $repository,
        ?CollectorInterface $collector
    ) {
        $this->calculator = $calculator;
        $this->aggregator = $aggregator;
        $this->merger = $merger;
        $this->repository = $repository;
        $this->collector = $collector ?? new Collector();
    }

    public function calculate($source, DateTimeImmutable $time = null): array
    {
        $charges = $this->calculateCharges($source, $time);
        $bills = $this->aggregator->aggregateCharges($charges);

        return $this->merger->mergeBills($bills);
    }

    public function perform($source, DateTimeImmutable $time = null): array
    {
        $charges = $this->calculateCharges($source, $time);
        $bills = $this->getRepoAggregator()->aggregateCharges($charges);

        return $this->saveBills($bills);
    }

    public function calculateCharges($source, DateTimeImmutable $time = null): array
    {
        $order = $this->collector->collect($source, $time);

        return $this->calculator->calculateOrder($order);
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
