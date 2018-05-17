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
use hiqdev\php\billing\context\ContextInterface;

/**
 * Price with formula
 * Provides charge modification (recalculation) with formula.
 *
 * @author Andrii Vasyliev <sol@hiqdev.com>
 */
trait FormulaChargeModifierTrait
{
    /**
     * @var ChargeModifier|null
     */
    protected $formula;

    /**
     * Calculate additional charges.
     *
     * @param ActionInterface $action
     * @param ChargeInterface $charge
     * @return ChargeInterface[] calculated charges
     */
    public function modifyCharge(?ChargeInterface $charge, ActionInterface $action, ContextInterface $context): array
    {
        return $this->formula ? $this->formula->modifyCharge($charge, $action, $context) : [];
    }

    public function setFormula(ChargeModifier $formula)
    {
        $this->formula = $formula;
    }
}
