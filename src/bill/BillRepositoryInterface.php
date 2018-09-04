<?php
/**
 * PHP Billing Library
 *
 * @link      https://github.com/hiqdev/php-billing
 * @package   php-billing
 * @license   BSD-3-Clause
 * @copyright Copyright (c) 2017-2018, HiQDev (http://hiqdev.com/)
 */

namespace hiqdev\php\billing\bill;

/**
 * @author Andrii Vasyliev <sol@hiqdev.com>
 */
interface BillRepositoryInterface
{
    /**
     * Finds in database ID of given bill.
     * @param BillInterface $bill
     * @return string|int|null
     */
    public function findId(BillInterface $bill);

    /**
     * Finds in database IDs of given bills.
     * @param BillInterface[] $ids
     * @return array
     */
    public function findIds(array $bills): array;

    /**
     * Finds bills by given ids.
     * @param int[] $ids
     * @return BillInterface[] array
     */
    public function findByIds(array $ids): array;
}
