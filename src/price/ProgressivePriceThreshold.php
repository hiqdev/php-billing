<?php

declare(strict_types=1);

namespace hiqdev\php\billing\price;

use hiqdev\php\units\Unit;
use Money\Money;
use hiqdev\php\units\Quantity;

class ProgressivePriceThreshold
{
    public string $price;

    public string $currency;

    public string $quantity;

    public string $unit;

    private function __construct(string $price, string $currency, string $quantity, string $unit)
    {
        if ($quantity < 0) {
            throw new \InvalidArgumentException('Quantity of the progressive price threshold must be positive');
        }

        $this->price = $price;
        $this->currency = $currency;
        $this->quantity = $quantity;
        $this->unit = $unit;
    }

    public static function createFromScalar(string $price, string $currency, string $quantity, string $unit): self
    {
        return new self($price, $currency, $quantity, $unit);
    }

    public static function createFromObjects(Money $price, Quantity $quantity)
    {
        return new self(
            $price->getAmount(),
            $price->getCurrency()->getCode(),
            $quantity->getQuantity(),
            $quantity->getUnit()->getName()
        );
    }

    /**
     * @return Money
     */
    public function price(): Money
    {
        return PriceHelper::buildMoney($this->price, $this->currency);
    }

    /**
     * @return Quantity
     */
    public function quantity(): Quantity
    {
        return Quantity::create(Unit::create($this->unit), $this->quantity);
    }

    public function getBasePrice(): string
    {
        return $this->price;
    }
}
