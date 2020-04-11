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
     * @var MergerInterface
     */
    protected $merger;
    /**
     * @var BillRepositoryInterface
     */
    private $billRepository;

    public function __construct(
        CalculatorInterface $calculator,
        AggregatorInterface $aggregator,
        MergerInterface $merger,
        ?BillRepositoryInterface $billRepository
    ) {
        $this->calculator = $calculator;
        $this->aggregator = $aggregator;
        $this->merger = $merger;
        $this->billRepository = $billRepository;
    }

    public function calculate(OrderInterface $order): array
    {
        $charges = $this->calculator->calculateOrder($order);
        $bills = $this->aggregator->aggregateCharges($charges);

        return $this->merger->mergeBills($bills);
    }

    public function perform(OrderInterface $order): array
    {
        $bills = $this->calculate($order);
        foreach ($bills as $bill) {
            $this->billRepository->save($bill);
        }

        return $bills;
    }
}
