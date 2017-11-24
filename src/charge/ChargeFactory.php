<?php
/**
 * PHP Billing Library
 *
 * @link      https://github.com/hiqdev/php-billing
 * @package   php-billing
 * @license   BSD-3-Clause
 * @copyright Copyright (c) 2017, HiQDev (http://hiqdev.com/)
 */

namespace hiqdev\php\billing\charge;

/**
 * Default charge factory.
 *
 * @author Andrii Vasyliev <sol@hiqdev.com>
 */
class ChargeFactory implements ChargeFactoryInterface
{
    /**
     * Creates charge object.
     * @return Charge
     */
    public function create(ChargeCreationDto $dto)
    {
        return new Charge($dto->id, $dto->name, $dto->seller, $dto->prices ?: []);
    }
}
