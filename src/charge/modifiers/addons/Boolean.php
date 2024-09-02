<?php
/**
 * PHP Billing Library
 *
 * @link      https://github.com/hiqdev/php-billing
 * @package   php-billing
 * @license   BSD-3-Clause
 * @copyright Copyright (c) 2017-2020, HiQDev (http://hiqdev.com/)
 */

namespace hiqdev\php\billing\charge\modifiers\addons;

use hiqdev\php\billing\charge\modifiers\AddonInterface;

/**
 * Boolean addon to flag something.
 *
 * @author Dmytro Naumenko <d.naumenko.a@gmail.com>
 */
readonly class Boolean implements AddonInterface
{
    public function __construct(
        public bool $value
    )
    {
    }
}
