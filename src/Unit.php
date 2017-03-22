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
 * Unit of measure.
 *
 * E.g.:
 * - byte:     matter=byte factor = 1
 * - megabyte: matter=byte factor = 10^6 = 1000000
 * - mebibyte: matter=byte factor = 2^20 = 1048576.
 *
 * @author Andrii Vasyliev <sol@hiqdev.com>
 */
class Unit implements UnitInterface
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
     * @var Unit
     */
    protected $base;

    /**
     * @var double
     * XXX we need some big number
     */
    protected $factor;

    /**
     * @inheritdoc
     */
    public function getMatter()
    {
        return $this->base->getName();
    }

    /**
     * @inheritdoc
     */
    public function getFactor(UnitInterface $other)
    {
        if (!$this->isComparable($other)) {
            throw new InvalidUnitConversion('');
        }

        return $other->factor / $this->factor;
    }

    /**
     * @inheritdoc
     */
    public function isComparable(UnitInterface $other)
    {
        return $this->getMatter() === $other->getMatter();
    }

    /**
     * @inheritdoc
     */
    public function convertTo(UnitInterface $other, double $quantity)
    {
        return $quantity * $other->getFactor($this);
    }

    /**
     * @inheritdoc
     */
    public function convertFrom(UnitInterface $other, $quantity)
    {
        if (!$this->isComparable($other)) {
            throw new InvalidUnitConversion('');
        }

        return $quantity * $this->getFactor($other);
    }
}
