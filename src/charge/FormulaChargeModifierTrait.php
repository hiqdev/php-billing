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

    /** {@inheritdoc} */
    public function modifyCharge(?ChargeInterface $charge, ActionInterface $action): array
    {
        return $this->formula ? $this->formula->modifyCharge($charge, $action) : [];
    }

    /** {@inheritdoc} */
    public function isSuitable(?ChargeInterface $charge, ActionInterface $action): bool
    {
        return $this->formula ? $this->formula->isSuitable($charge, $action) : false;
    }

    public function setFormula(ChargeModifier $formula)
    {
        $this->formula = $formula;
    }
}
