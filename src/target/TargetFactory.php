<?php
/**
 * PHP Billing Library
 *
 * @link      https://github.com/hiqdev/php-billing
 * @package   php-billing
 * @license   BSD-3-Clause
 * @copyright Copyright (c) 2017-2020, HiQDev (http://hiqdev.com/)
 */

namespace hiqdev\php\billing\target;

/**
 * Default target factory.
 *
 * @author Andrii Vasyliev <sol@hiqdev.com>
 */
class TargetFactory implements TargetFactoryInterface
{
    public function create(TargetCreationDto $dto): TargetInterface
    {
        return new Target($dto->id, $dto->type, $dto->name);
    }

    public function getClassForType(string $type): string
    {
        return Target::class;
    }

    public function shortenType(string $type): string
    {
        return $type;
    }
}
