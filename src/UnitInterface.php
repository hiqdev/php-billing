<?php
/**
 * PHP Billing Library
 *
 * @link      https://github.com/hiqdev/php-billing
 * @package   php-billing
 * @license   BSD-3-Clause
 * @copyright Copyright (c) 2017, HiQDev (http://hiqdev.com/)
 */

namespace hiqdev\php\billing;

/**
 * Unit of measurement.
 * For converting raw amount to unit quantity and back.
 * E.g.:
 * - megabyte: factor = 10^6 = 1000000
 * - mebibyte: factor = 2^20 = 1048576.
 *
 * @author Andrii Vasyliev <sol@hiqdev.com>
 */
interface UnitInterface
{
    /**
     * Get name.
     * @return string
     */
    public function getName();

    /**
     * Get matter this unit measures.
     * @return string
     */
    public function getMatter();

    /**
     * Get factor for other unit.
     * @param UnitInterface $other
     * @return string
     */
    public function getFactor(UnitInterface $other);

    /**
     * Checks whether the given unit is the same as this.
     * @param UnitInterface $other
     */
    public function equals(UnitInterface $other);

    /**
     * Checks whether the given unit is comparable to this.
     * @param UnitInterface $other
     */
    public function isComparable(UnitInterface $other);

    /**
     * Converts to other unit quantity.
     * @param UnitInterface $other
     * @param double $amount raw amount
     * @return double unit quantity
     */
    public function convertTo(UnitInterface $other, $amount);

    /**
     * Converts from other unit quantity.
     * @param UnitInterface $other
     * @param double unit quantity
     * @return double $amount raw amount
     */
    public function convertFrom(UnitInterface $other, $quantity);
}
