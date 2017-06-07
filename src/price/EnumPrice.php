<?php
/**
 * PHP Billing Library
 *
 * @link      https://github.com/hiqdev/php-billing
 * @package   php-billing
 * @license   BSD-3-Clause
 * @copyright Copyright (c) 2017, HiQDev (http://hiqdev.com/)
 */

namespace hiqdev\php\billing\price;

use hiqdev\php\billing\target\TargetInterface;
use hiqdev\php\billing\type\TypeInterface;
use hiqdev\php\units\UnitInterface;
use hiqdev\php\units\QuantityInterface;

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
                            $id,
        TypeInterface       $type,
        TargetInterface     $target,
        UnitInterface       $unit,
        array               $enum
    ) {
        parent::__construct($id, $type, $target);
        $this->unit = $unit;
        $this->enum = $enum;
    }

    /**
     * {@inheritdoc}
     */
    public function calculateSum(QuantityInterface $quantity)
    {
        return $this->calculatePrice($quantity);
    }

    /**
     * {@inheritdoc}
     */
    public function calculatePrice(QuantityInterface $quantity)
    {
        $usage = (string) $this->calculateUsage($quantity)->getQuantity();
        foreach ($this->enum as $value => $price) {
            if ((string)$value === (string)$usage) {
                return $price;
            }
        }

        throw new FailedCalculatePriceException('not enumed quantity');
    }

    /**
     * {@inheritdoc}
     */
    public function calculateUsage(QuantityInterface $quantity)
    {
        return $quantity->convert($this->unit);
    }
}
