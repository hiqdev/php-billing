<?php
/**
 * PHP Billing Library
 *
 * @link      https://github.com/hiqdev/php-billing
 * @package   php-billing
 * @license   BSD-3-Clause
 * @copyright Copyright (c) 2017-2020, HiQDev (http://hiqdev.com/)
 */

namespace hiqdev\php\billing\bill;

/**
 * Default bill factory.
 *
 * @author Andrii Vasyliev <sol@hiqdev.com>
 */
class BillFactory implements BillFactoryInterface
{
    /**
     * Creates bill object.
     * @return Bill
     */
    public function create(BillCreationDto $dto)
    {
        return new Bill(
            $dto->id,
            $dto->type,
            $dto->time,
            $dto->sum,
            $dto->quantity,
            $dto->customer,
            $dto->target,
            $dto->plan,
            $dto->charges ?: [],
            $dto->state,
            $dto->from ?? null
        );
    }
}
