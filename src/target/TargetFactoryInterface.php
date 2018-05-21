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
 * Target factory interface.
 *
 * @author Andrii Vasyliev <sol@hiqdev.com>
 */
interface TargetFactoryInterface
{
    /**
     * @return Target
     */
    public function create(TargetCreationDto $dto);

    /**
     * Returns class that represents target with $type.
     *
     * @param string $type the target type
     * @return string
     */
    public function getClassForType(string $type): string;

    /**
     * Ensures type does not contain subtype.
     *
     * @param string $type
     * @return string
     */
    public function shortenType(string $type): string;
}
