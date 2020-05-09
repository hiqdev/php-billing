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
 * Charge factory interface.
 *
 * @author Andrii Vasyliev <sol@hiqdev.com>
 */
interface ChargeFactoryInterface
{
    /**
     * Creates charge object.
     * @return Charge
     */
    public function create(ChargeCreationDto $dto);
}
