<?php
/**
 * PHP Billing Library
 *
 * @link      https://github.com/hiqdev/php-billing
 * @package   php-billing
 * @license   BSD-3-Clause
 * @copyright Copyright (c) 2017-2020, HiQDev (http://hiqdev.com/)
 */

namespace hiqdev\php\billing\tools;

use hiqdev\php\billing\bill\BillInterface;

/**
 * @author Andrii Vasyliev <sol@hiqdev.com>
 */
interface MergerInterface
{
    /**
     * Merges array of bills.
     * @param BillInterface[] $bills
     * @return BillInterface[]
     */
    public function mergeBills(array $bills): array;

    /**
     * Merges two bills in one.
     */
    public function mergeBill(BillInterface $first, BillInterface $other): BillInterface;
}
