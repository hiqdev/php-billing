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
use hiqdev\php\billing\charge\modifiers\AddonInterface;

/**
 * Date addon.
 *
 * @author Andrii Vasyliev <sol@hiqdev.com>
 */
abstract class Date implements AddonInterface
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

        if (preg_match('/^(\d{4})-(\d{2})$/', $value, $ms)) {
            return new DateTimeImmutable("$ms[1]-$ms[2]-01");
        }

        if (preg_match('/^(\d{4})-(\d{1,2})-(\d{1,2})$/', $value, $ms)) {
            return new DateTimeImmutable("$ms[1]-$ms[2]-$ms[3]");
        }

        if (preg_match('/^(\d{2})\.(\d{4})$/', $value, $ms)) {
            return new DateTimeImmutable("$ms[2]-$ms[1]-01");
        }

        if (preg_match('/^(\d{1,2})\.(\d{1,2})\.(\d{4})$/', $value, $ms)) {
            return new DateTimeImmutable("$ms[3]-$ms[2]-$ms[1]");
        }

        throw new \Exception('invalid date given');
    }
}
