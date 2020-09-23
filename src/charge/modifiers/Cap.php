<?php
/**
 * PHP Billing Library
 *
 * @link      https://github.com/hiqdev/php-billing
 * @package   php-billing
 * @license   BSD-3-Clause
 * @copyright Copyright (c) 2017-2020, HiQDev (http://hiqdev.com/)
 */

namespace hiqdev\php\billing\charge\modifiers;

/**
 * Cap represents the upper limit in billing.
 *
 * @author Andrii Vasyliev <sol@hiqdev.com>
 */
class Cap extends Modifier
{
    public function monthly(string $capDuration): MonthlyCap
    {
        return new MonthlyCap($capDuration, $this->addons);
    }
}
