<?php
/**
 * PHP Billing Library
 *
 * @link      https://github.com/hiqdev/php-billing
 * @package   php-billing
 * @license   BSD-3-Clause
 * @copyright Copyright (c) 2017-2020, HiQDev (http://hiqdev.com/)
 */

namespace hiqdev\php\billing\charge;

/**
 * Default charge factory.
 *
 * @author Andrii Vasyliev <sol@hiqdev.com>
 */
class ChargeFactory implements ChargeFactoryInterface
{
    public function create(ChargeCreationDto $dto): ChargeInterface
    {
        return new Charge(
            $dto->id,
            $dto->type,
            $dto->target,
            $dto->action,
            $dto->price,
            $dto->usage,
            $dto->sum,
            $dto->bill
        );
    }
}
