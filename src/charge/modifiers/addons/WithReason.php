<?php
/**
 * PHP Billing Library
 *
 * @link      https://github.com/hiqdev/php-billing
 * @package   php-billing
 * @license   BSD-3-Clause
 * @copyright Copyright (c) 2017-2018, HiQDev (http://hiqdev.com/)
 */

namespace hiqdev\php\billing\charge\modifiers\addons;

/**
 * With Reason trait.
 *
 * @author Andrii Vasyliev <sol@hiqdev.com>
 */
trait WithReason
{
    public function reason($text)
    {
        return $this->addAddon('reason', new Reason($text));
    }

    public function getReason()
    {
        return $this->getAddon('reason');
    }
}
