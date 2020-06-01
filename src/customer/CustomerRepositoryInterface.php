<?php
/**
 * PHP Billing Library
 *
 * @link      https://github.com/hiqdev/php-billing
 * @package   php-billing
 * @license   BSD-3-Clause
 * @copyright Copyright (c) 2017-2020, HiQDev (http://hiqdev.com/)
 */

namespace hiqdev\php\billing\customer;

/**
 * @author Andrii Vasyliev <sol@hiqdev.com>
 */
interface CustomerRepositoryInterface
{
    /**
     * Finds bills by unique fields.
     * @param CustomerInterface[] $bills
     * @return CustomerInterface[]
     */
    public function findByUniqueness(array $customers): array;

    /**
     * Save bill to storage.
     * @return string|int|null
     */
    public function save(CustomerInterface $bill);

    /**
     * @return object|false
     */
    public function findOne($specification);
}
