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
 * Discount maximum addon.
 *
 * @author Andrii Vasyliev <sol@hiqdev.com>
 */
class Maximum extends Extremum
{
    protected static $name = 'maximum';
}
