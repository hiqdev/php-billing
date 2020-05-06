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

class SimpleBillRepository implements BillRepositoryInterface
{
    protected $bills = [];

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
        $this->bills[$bill->getUniqueString()] = $bill;

        return $bill->getId();
    }

    public function findByUniqueness(array $bills): array
    {
        $found = [];
        foreach ($this->bills as $bill) {
            foreach ($bills as $one) {
                if (
                    !$bill->getType()->equals($one->getType()) ||
                    !$bill->getTarget()->equals($one->getTarget())
                ) {
                    continue;
                }
                $found[] = $bill;
            }
        }

        return $found;
    }
}
