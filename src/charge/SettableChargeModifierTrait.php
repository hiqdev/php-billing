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
 * Adds modifier property and implements ChargeModifier interface.
 *
 * @author Andrii Vasyliev <sol@hiqdev.com>
 */
trait SettableChargeModifierTrait
{
    /**
     * @var ChargeModifier|null
     */
    protected $modifier;

    /** {@inheritdoc} */
    public function modifyCharge(?ChargeInterface $charge, ActionInterface $action): array
    {
        if ($this->modifier !== null) {
            return $this->modifier->modifyCharge($charge, $action);
        }

        return $charge ? [$charge] : [];
    }

    /** {@inheritdoc} */
    public function isSuitable(?ChargeInterface $charge, ActionInterface $action): bool
    {
        return $this->modifier ? $this->modifier->isSuitable($charge, $action) : false;
    }

    public function setModifier(ChargeModifier $modifier)
    {
        $this->modifier = $modifier;
    }
}
