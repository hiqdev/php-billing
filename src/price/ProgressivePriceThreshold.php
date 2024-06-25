<?php

declare(strict_types=1);

namespace hiqdev\php\billing\price;

use InvalidArgumentException;
use JsonSerializable;
use hiqdev\php\units\Unit;
use Money\Money;
use hiqdev\php\units\Quantity;

class ProgressivePriceThreshold implements JsonSerializable
{
    private string $price;

    private string $currency;

    private string $quantity;

    private string $unit;

    private function __construct(string $price, string $currency, string $quantity, string $unit)
    {
        if ($quantity < 0) {
            throw new InvalidArgumentException('Quantity of the progressive price threshold must be positive');
        }

        $this->price = $price;
        $this->currency = strtoupper($currency);
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

    public function price(): ?Money
    {
        return MoneyBuilder::buildMoney($this->price, $this->currency);
    }

    public function quantity(): Quantity
    {
        return Quantity::create(Unit::create($this->unit), $this->quantity);
    }

    public function getBasePrice(): string
    {
        return $this->price;
    }

    public function __toArray(): array
    {
        return [
            'price' => $this->price,
            'currency' => $this->currency,
            'quantity' => $this->quantity,
            'unit' => $this->unit,
        ];
    }

    #[\ReturnTypeWillChange]
    public function jsonSerialize()
    {
        return get_object_vars($this);
    }
}
