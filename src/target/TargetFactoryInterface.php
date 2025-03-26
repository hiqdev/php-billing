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
 * Target factory interface.
 *
 * @author Andrii Vasyliev <sol@hiqdev.com>
 */
interface TargetFactoryInterface
{
    public function create(TargetCreationDto $dto): TargetInterface;

    /**
     * Returns class that represents target with $type.
     *
     * @param string $type the target type
     */
    public function getClassForType(string $type): string;

    /**
     * Ensures type does not contain subtype.
     * XXX should be removed.
     */
    public function shortenType(string $type): string;
}
