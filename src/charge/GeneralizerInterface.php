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

use hiqdev\php\billing\bill\BillInterface;
use hiqdev\php\billing\target\TargetInterface;
use hiqdev\php\billing\type\TypeInterface;

/**
 * @author Andrii Vasyliev <sol@hiqdev.com>
 */
interface GeneralizerInterface
{
    /**
     * Creates generalized Bill from given charge.
     */
    public function createBill(ChargeInterface $charge): BillInterface;

    /**
     * Finds less general (more specific) type from given ones.
     */
    public function specializeType(TypeInterface $first, TypeInterface $other): TypeInterface;

    /**
     * Finds less general (more specific) target from given ones.
     */
    public function specializeTarget(TargetInterface $first, TargetInterface $other): TargetInterface;
}
