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
 * Tariff Interface.
 */
interface TariffInterface
{
    /**
     * Calculates charges for given action.
     * @param ActionInterface $action
     * @return ChargeInterface[]
     */
    public function calculateCharges(ActionInterface $action);
}
