<?php
/**
 * PHP Billing Library
 *
 * @link      https://github.com/hiqdev/php-billing
 * @package   php-billing
 * @license   BSD-3-Clause
 * @copyright Copyright (c) 2017-2018, HiQDev (http://hiqdev.com/)
 */

namespace hiqdev\php\billing\tools;

use hiqdev\php\billing\bill\Bill;
use hiqdev\php\billing\bill\BillInterface;
use hiqdev\php\units\QuantityInterface;
use Money\Money;

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
     * @param BillInterface $first
     * @param BillInterface $other
     * @return BillInterface
     */
    public function mergeBill(BillInterface $first, BillInterface $other): BillInterface;
}
