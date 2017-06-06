<?php
/**
 * PHP Billing Library
 *
 * @link      https://github.com/hiqdev/php-billing
 * @package   php-billing
 * @license   BSD-3-Clause
 * @copyright Copyright (c) 2017, HiQDev (http://hiqdev.com/)
 */

namespace hiqdev\php\billing;

/**
 * @author Andrii Vasyliev <sol@hiqdev.com>
 */
class TypeFactory implements TypeFactoryInterface
{
    /**
     * Creates bill object.
     * @return Type
     */
    public function create(TypeCreationDto $dto)
    {
        return new Type($dto->id, $dto->name);
    }
}
