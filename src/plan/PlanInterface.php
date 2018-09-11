<?php
/**
 * PHP Billing Library
 *
 * @link      https://github.com/hiqdev/php-billing
 * @package   php-billing
 * @license   BSD-3-Clause
 * @copyright Copyright (c) 2017-2018, HiQDev (http://hiqdev.com/)
 */

namespace hiqdev\php\billing\plan;

use hiqdev\php\billing\action\ActionInterface;
use hiqdev\php\billing\charge\ChargeInterface;

/**
 * Plan Interface.
 *
 * @author Andrii Vasyliev <sol@hiqdev.com>
 */
interface PlanInterface
{
    /**
     * @return int|string
     */
    public function getId();

    /**
     * Globally unique ID.
     * @return int|string
     */
    public function getUniqueId();
}
