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
class Unit
{
    /**
     * @var integer
     */
    protected $id;

    /**
     * @var string
     */
    protected $name;

    /**
     * @var double
     * XXX we need some big number
     */
    protected $factor;

    /**
     * Converts raw amount to unit quantity.
     * @param double $amount raw amount
     * @return double unit quantity
     */
    public function convertTo($amount)
    {
        return $amount / $this->factor;
    }

    /**
     * Converts unit quantity to raw amount.
     * @param double unit quantity
     * @return double $amount raw amount
     */
    public function convertFrom($quantity)
    {
        return $quantity * $this->factor;
    }
}
