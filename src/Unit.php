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
 * - byte:     parent=byte factor = 1
 * - megabyte: parent=byte factor = 10^6 = 1000000
 * - mebibyte: parent=byte factor = 2^20 = 1048576.
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
    protected $parent;

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
        return $this->parent->getName();
    }

    /**
     * @inheritdoc
     */
    public function getFactor(UnitInterface $other)
    {
        if (!$this->isComparable($other)) {
            throw new InvalidUnitConversionException('');
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
        return $quantity * $this->getFactor($other);
    }

    public static function __callStatic($name)
    {
        return new Unit();
    }
}
