<?php
/**
 * PHP Billing Library
 *
 * @link      https://github.com/hiqdev/php-billing
 * @package   php-billing
 * @license   BSD-3-Clause
 * @copyright Copyright (c) 2017-2018, HiQDev (http://hiqdev.com/)
 */

namespace hiqdev\php\billing\target;

/**
 * Default target factory.
 *
 * @author Andrii Vasyliev <sol@hiqdev.com>
 */
class TargetFactory implements TargetFactoryInterface
{
    /**
     * @return Target
     */
    public function create(TargetCreationDto $dto)
    {
        return new Target($dto->id, $dto->type);
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
