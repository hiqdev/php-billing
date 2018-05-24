<?php
/**
 * PHP Billing Library
 *
 * @link      https://github.com/hiqdev/php-billing
 * @package   php-billing
 * @license   BSD-3-Clause
 * @copyright Copyright (c) 2017-2018, HiQDev (http://hiqdev.com/)
 */

namespace hiqdev\php\billing\charge\modifiers;

use hiqdev\php\billing\action\ActionInterface;
use hiqdev\php\billing\charge\ChargeInterface;
use hiqdev\php\billing\charge\ChargeModifier;

/**
 * Last combination.
 *
 * @author Andrii Vasyliev <sol@hiqdev.com>
 */
class LastCombination implements ChargeModifier
{
    public function __construct(ChargeModifier $left, ChargeModifier $right)
    {
        $this->left = $left;
        $this->right = $right;
    }

    public function modifyCharge(?ChargeInterface $charge, ActionInterface $action): array
    {
        if ($this->right->isSuitable($charge, $action)) {
            return $this->right->modifyCharge($charge, $action);
        }

        return $this->left->modifyCharge($charge, $action);
    }

    public function isSuitable(?ChargeInterface $charge, ActionInterface $action): bool
    {
        return $this->left->isSuitable($charge, $action) || $this->right->isSuitable($charge, $action);
    }
}
