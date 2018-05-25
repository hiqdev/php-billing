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
 * With Since trait.
 *
 * @author Andrii Vasyliev <sol@hiqdev.com>
 */
trait WithSince
{
    public function since($time)
    {
        return $this->addAddon('since', new Since($time));
    }

    public function getSince()
    {
        return $this->getAddon('since');
    }
}
