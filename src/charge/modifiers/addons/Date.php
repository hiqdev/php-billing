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

use DateTimeImmutable;

/**
 * Date addon.
 *
 * @author Andrii Vasyliev <sol@hiqdev.com>
 */
class Date
{
    /**
     * @var DateTimeImmutable
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
        if ($value instanceof DateTimeImmutable) {
            return $value;
        }

        throw new \Exception('invalid date given');
    }
}
