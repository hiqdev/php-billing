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
 * Percent point.
 *
 * @author Andrii Vasyliev <sol@hiqdev.com>
 */
class PercentPoint
{
    /**
     * @var int|float|string
     */
    private $number;

    /**
     * @param int|float|string $number
     */
    public function __construct($number)
    {
        $this->number = $number;
    }

    /**
     * @return int|float|string
     */
    public function getNumber()
    {
        return $this->number;
    }
}
