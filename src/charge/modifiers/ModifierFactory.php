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
 * Modifier Factory.
 *
 * @author Andrii Vasyliev <sol@hiqdev.com>
 */
class ModifierFactory
{
    public function __call($name, $args)
    {
        $res = new Modifier();

        return call_user_func_array([$res, $name], $args);
    }
}
