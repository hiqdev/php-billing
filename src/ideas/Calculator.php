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
 * Charges calculator.
 *
 * @author Andrii Vasyliev <sol@hiqdev.com>
 */
class Calculator implements TariffInterface
{

    /**
     * Calculate charges for given action.
     * @param ActionInterface $action
     * @return Charge[]
     */
    public function calculateCharges(TariffInterface $tariff, ActionInterface $action)
    {
        $charges = [];
        foreach ($tariff->getPrices() as $price) {
            $charge = $price->calculateCharge($action);
            if ($charge !== null) {
                $charges[] = $charge;
            }
        }

        return $charges;
    }
}
