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

use hiqdev\php\billing\charge\modifiers\AddonInterface;

/**
 * Period addon.
 *
 * @author Andrii Vasyliev <sol@hiqdev.com>
 */
abstract class Period implements AddonInterface
{
    /**
     * @var int
     */
    protected $value;

    public function __construct($value)
    {
        $this->value = static::ensureValidValue($value);
    }

    public function getValue()
    {
        return $this->value;
    }

    public static function ensureValidValue($value)
    {
        if (filter_var($value, FILTER_VALIDATE_INT) === false) {
            throw new \Exception('periodicity must be integer number');
        }

        return (int) $value;
    }
}
