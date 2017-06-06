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
use hiqdev\php\units\Quantity;
use hiqdev\php\units\QuantityInterface;
use Money\Money;

/**
 * Single Price.
 *
 * - no charge for quantity less then prepaid
 * - same price for any quantity above prepaid
 *
 * @see PriceInterface
 *
 * @author Andrii Vasyliev <sol@hiqdev.com>
 */
class SinglePrice extends AbstractPrice
{
    /**
     * @var Quantity prepaid quantity also implies Unit
     */
    protected $prepaid;

    /**
     * @var Money
     */
    protected $price;

    public function __construct(
                            $id,
        TargetInterface     $target,
        TypeInterface       $type,
        QuantityInterface   $prepaid,
        Money               $price
    ) {
        parent::__construct($id, $target, $type);
        $this->prepaid  = $prepaid;
        $this->price    = $price;
    }

/*
    protected $properties = [
        'id'        => '',
        'target'    => [AbstractTarget::class, 'create'],
        'type'      => [Type::class, 'create'],
        'prepaid'   => [Quantity::class, 'create'],
        'price'     => [MoneyFactory::class, 'create'],
    ];
*/

    /**
     * {@inheritdoc}
     */
    public function calculateUsage(QuantityInterface $quantity)
    {
        $usage = $quantity->convert($this->prepaid->getUnit())->subtract($this->prepaid);

        return $usage->isPositive() ? $usage : null;
    }

    /**
     * {@inheritdoc}
     * Same price for any usage.
     */
    public function calculatePrice(QuantityInterface $usage)
    {
        return $this->price;
    }
}
