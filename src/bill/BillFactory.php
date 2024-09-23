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
    public function create(BillCreationDto $dto): BillInterface
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
            $dto->state
        );
    }
}
