<?php
/**
 * PHP Billing Library
 *
 * @link      https://github.com/hiqdev/php-billing
 * @package   php-billing
 * @license   BSD-3-Clause
 * @copyright Copyright (c) 2017, HiQDev (http://hiqdev.com/)
 */

namespace hiqdev\php\billing\type;

/**
 * @author Andrii Vasyliev <sol@hiqdev.com>
 */
class TypeFactory implements TypeFactoryInterface
{
    /**
     * @return Type
     */
    public function create(TypeCreationDto $dto)
    {
        return new Type($dto->id, $dto->name);
    }
}
