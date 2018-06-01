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
use hiqdev\php\billing\formula\FormulaSemanticsError;

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

    protected static $periods = [
        'month'     => MonthPeriod::class,
        'months'    => MonthPeriod::class,
        'year'      => YearPeriod::class,
        'years'     => YearPeriod::class,
    ];

    public static function fromString($string)
    {
        if (preg_match('/^((\d+) +)?(\w+)$/', trim($string), $ms)) {
            if (isset(static::$periods[$ms[3]])) {
                $class = static::$periods[$ms[3]];

                return new $class($ms[1] ?: 1);
            }
        }

        throw new FormulaSemanticsError("invalid period given: $string");
    }

    public static function ensureValidValue($value)
    {
        if (filter_var($value, FILTER_VALIDATE_INT) === false) {
            throw new FormulaSemanticsError('periodicity must be integer number');
        }

        return (int) $value;
    }
}
