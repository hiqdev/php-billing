<?php
/**
 * PHP Billing Library
 *
 * @link      https://github.com/hiqdev/php-billing
 * @package   php-billing
 * @license   BSD-3-Clause
 * @copyright Copyright (c) 2017-2018, HiQDev (http://hiqdev.com/)
 */

namespace hiqdev\php\billing\charge;

use hiqdev\php\billing\action\ActionInterface;

/**
 * ChargeModifier interface.
 * @see FormulaChargeModifierTrait
 *
 * @author Andrii Vasyliev <sol@hiqdev.com>
 */
interface ChargeModifier
{
    /**
     * Calculates modified charges.
     *
     * @param ChargeInterface $charge
     * @param ActionInterface $action
     * @return ChargeInterface[] calculated charges
     */
    public function modifyCharge(?ChargeInterface $charge, ActionInterface $action): array;

    /**
     * Returns true if modifier is applicable for the given charge
     * (due to time or other limitations)
     *
     * @param ChargeInterface $charge
     * @param ActionInterface $action
     * @return ChargeInterface[] calculated charges
     */
    public function isSuitable(?ChargeInterface $charge, ActionInterface $action): bool;
}
