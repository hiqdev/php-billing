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
     * Finds bills by unique fields.
     * @param BillInterface[] $bills
     * @return BillInterface[]
     */
    public function findByUniqueness(array $bills): array;

    /**
     * Save bill to storage.
     * @return string|int|null
     */
    public function save(BillInterface $bill);
}
