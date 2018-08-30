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
 * With Till trait.
 *
 * @author Andrii Vasyliev <sol@hiqdev.com>
 */
trait WithTill
{
    public function till($time)
    {
        return $this->addAddon('till', new Till($time));
    }

    public function getTill(): ?Till
    {
        return $this->getAddon('till');
    }
}
