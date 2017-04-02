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
 * Enum Price:
 * - listed quantities only else exception.
 * @see PriceInterface
 *
 * @author Andrii Vasyliev <sol@hiqdev.com>
 */
class EnumPrice extends AbstractPrice
{
    /**
     * @var UnitInterface
     */
    protected $unit;

    /**
     * @var MoneyInterface[]
     */
    protected $enum;

    public function __construct(
        TargetInterface     $target,
        TypeInterface       $type,
        UnitInterface       $unit,
        array               $enum
    ) {
        parent::__construct($target, $type);
        $this->unit = $unit;
        $this->enum = $enum;
    }

    /**
     * {@inheritdoc}
     */
    public function calculateUsage(QuantityInterface $quantity)
    {
        return $quantity->convert($this->unit);
    }

    /**
     * {@inheritdoc}
     */
    public function calculatePrice(QuantityInterface $usage)
    {
        foreach ($this->enum as $quantity => $price) {
            if ($usage->equals($quantity)) {
                return $price;
            }
        }

        throw new InvalidQuantityException('');
    }
}
