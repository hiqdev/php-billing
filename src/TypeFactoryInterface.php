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
 * Type factory interface.
 *
 * @author Andrii Vasyliev <sol@hiqdev.com>
 */
interface TypeFactoryInterface
{
    /**
     * Creates bill object.
     * @return Type
     */
    public function create(TypeCreationDto $dto);
}
