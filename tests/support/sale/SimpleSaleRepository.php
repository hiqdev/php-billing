<?php
/**
 * PHP Billing Library
 *
 * @link      https://github.com/hiqdev/php-billing
 * @package   php-billing
 * @license   BSD-3-Clause
 * @copyright Copyright (c) 2017-2020, HiQDev (http://hiqdev.com/)
 */

namespace hiqdev\php\billing\tests\support\sale;

use Exception;
use hiqdev\php\billing\action\ActionInterface;
use hiqdev\php\billing\order\OrderInterface;
use hiqdev\php\billing\sale\SaleInterface;
use hiqdev\php\billing\sale\SaleRepositoryInterface;
use hiqdev\DataMapper\Query\Specification;
use DateTimeImmutable;

class SimpleSaleRepository implements SaleRepositoryInterface
{
    protected $sale;

    public function __construct(?SaleInterface $sale = null)
    {
        $this->sale = $sale;
    }

    public function findId(SaleInterface $sale)
    {
        return $this->sale->getId();
    }

    public function findByAction(ActionInterface $action)
    {
        return $this->sale;
    }

    public function findByOrder(OrderInterface $order)
    {
        $sales = [];
        foreach ($order->getActions() as $actionKey => $action) {
            $sales[$actionKey] = $this->findByAction($action);
        }

        return $sales;
    }

    public function findAllActive(Specification $specification, ?DateTimeImmutable $time): array
    {
        throw new Exception('not implemented');
    }

    public function findByIds(array $ids)
    {
        throw new Exception('not implemented');
    }

    public function findById(string $id): ?object
    {
        throw new Exception('not implemented');
    }

    public function isTariffInUse(int $tariffId): bool
    {
        throw new Exception('not implemented');
    }

    public function deleteByTariffId(int $tariffId): void
    {
        throw new Exception('not implemented');
    }
}
