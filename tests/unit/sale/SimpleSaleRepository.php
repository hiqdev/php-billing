<?php
/**
 * PHP Billing Library
 *
 * @link      https://github.com/hiqdev/php-billing
 * @package   php-billing
 * @license   BSD-3-Clause
 * @copyright Copyright (c) 2017-2018, HiQDev (http://hiqdev.com/)
 */

namespace hiqdev\php\billing\tests\unit\sale;

use hiqdev\php\billing\action\ActionInterface;
use hiqdev\php\billing\order\OrderInterface;
use hiqdev\php\billing\sale\SaleInterface;
use hiqdev\php\billing\sale\SaleRepositoryInterface;

class SimpleSaleRepository implements SaleRepositoryInterface
{
    protected $sale;

    public function __construct(SaleInterface $sale)
    {
        $this->sale = $sale;
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

    public function findByIds(array $ids)
    {
        throw new \Exception('not implemented');
    }
}
