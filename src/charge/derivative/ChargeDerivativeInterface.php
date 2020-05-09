<?php
/**
 * PHP Billing Library
 *
 * @link      https://github.com/hiqdev/php-billing
 * @package   php-billing
 * @license   BSD-3-Clause
 * @copyright Copyright (c) 2017-2020, HiQDev (http://hiqdev.com/)
 */

namespace hiqdev\php\billing\charge\derivative;

use hiqdev\php\billing\charge\ChargeInterface;

/**
 * Interface ChargeDerivativeInterface creates a copy of the passed charge
 * with changes, according to the [[ChargeDerivativeQueryInterface]]
 *
 * @see ChargeDerivativeQueryInterface
 *
 * @author Dmytro Naumenko <d.naumenko.a@gmail.com>
 */
interface ChargeDerivativeInterface
{
    public function __invoke(ChargeInterface $charge, ChargeDerivativeQueryInterface $query): ChargeInterface;
}
