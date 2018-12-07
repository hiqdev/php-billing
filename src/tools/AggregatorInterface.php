<?php
/**
 * PHP Billing Library
 *
 * @link      https://github.com/hiqdev/php-billing
 * @package   php-billing
 * @license   BSD-3-Clause
 * @copyright Copyright (c) 2017-2018, HiQDev (http://hiqdev.com/)
 */

namespace hiqdev\php\billing\tools;

use hiqdev\php\billing\bill\Bill;
use hiqdev\php\billing\charge\Charge;

/**
 * @author Andrii Vasyliev <sol@hiqdev.com>
 */
interface AggregatorInterface
{
    /**
     * Aggregates given charges to Bills.
     * @param Charge[] $charges array (can be nested) of charges
     * @return Bill[]
     */
    public function aggregateCharges(array $charges): array;
}
