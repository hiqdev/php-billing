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

use hiqdev\php\billing\bill\Bill;

/**
 * @author Andrii Vasyliev <sol@hiqdev.com>
 */
interface GeneralizerInterface
{
    /**
     * Creates generalized Bill from given charge.
     * @param ChargeInterface $charge
     * @return Bill
     */
    public function createBill(ChargeInterface $charge);
}
