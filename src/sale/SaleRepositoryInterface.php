<?php
/**
 * PHP Billing Library
 *
 * @link      https://github.com/hiqdev/php-billing
 * @package   php-billing
 * @license   BSD-3-Clause
 * @copyright Copyright (c) 2017-2020, HiQDev (http://hiqdev.com/)
 */

namespace hiqdev\php\billing\sale;

use hiqdev\php\billing\order\OrderInterface;
use hiqdev\php\billing\plan\PlanInterface;
use hiqdev\DataMapper\Query\Specification;
use DateTimeImmutable;

/**
 * @author Andrii Vasyliev <sol@hiqdev.com>
 */
interface SaleRepositoryInterface
{
    /**
     * @param \hiqdev\php\billing\sale\SaleInterface $sale
     * @return string
     */
    public function findId(SaleInterface $sale);

    /**
     * @param $id
     * @return SaleInterface|null
     */
    public function findById(string $id): ?object;

    /**
     * Finds suitable sales for given order.
     * @return PlanInterface[] array: actionKey => plan
     */
    public function findByOrder(OrderInterface $order);

    /**
     * Find all active sales at given time
     */
    public function findAllActive(Specification $specification, ?DateTimeImmutable $time): array;
}
