<?php
/**
 * PHP Billing Library
 *
 * @link      https://github.com/hiqdev/php-billing
 * @package   php-billing
 * @license   BSD-3-Clause
 * @copyright Copyright (c) 2017-2018, HiQDev (http://hiqdev.com/)
 */

namespace hiqdev\php\billing\tests\support\bill;

use hiqdev\php\billing\bill\BillInterface;
use hiqdev\php\billing\bill\BillRepositoryInterface;
use hiqdev\php\billing\target\TargetInterface;

class SimpleBillRepository implements BillRepositoryInterface
{
    protected $bills;

    public function findId(BillInterface $bill)
    {
        throw new \Exception('not implemented');
    }

    public function findIds(array $bills): array
    {
        throw new \Exception('not implemented');
    }

    public function findByIds(array $ids): array
    {
        $bills = [];
        foreach ($ids as $id) {
            if (empty($this->bills[$id])) {
                continue;
            }
            $bills[$id] = $this->bills[$id];
        }

        return $bills;
    }

    public function save(BillInterface $bill)
    {
        $id = $bill->getId();
        $this->bills[$id] = $bill;

        return $id;
    }

    public function findByUniqueness(BillInterface $bill)
    {
        return $this->bills;
    }
}
