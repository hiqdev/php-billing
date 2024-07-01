<?php

declare(strict_types=1);

namespace hiqdev\php\billing\price;

use hiqdev\php\billing\Money\MultipliedMoney;
use hiqdev\php\units\Unit;
use hiqdev\php\units\UnitInterface;
use InvalidArgumentException;
use JsonSerializable;
use Money\Money;
use hiqdev\php\units\Quantity;

class ProgressivePriceThreshold implements JsonSerializable
{
    /**
     * @var numeric-string $price The price of the progressive price threshold in currency (not cents)
     */
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

    public static function createFromObjects(Money $price, Quantity $quantity): self
    {
        return new self(
            (string)((int)$price->getAmount() / 100), // TODO: Might be not 100 for some currencies
            $price->getCurrency()->getCode(),
            (string)$quantity->getQuantity(),
            $quantity->getUnit()->getName()
        );
    }

    public function price(): ?MultipliedMoney
    {
        return MultipliedMoney::create($this->price, $this->currency);
    }

    public function quantity(): Quantity
    {
        return Quantity::create($this->unit, $this->quantity);
    }

    public function unit(): UnitInterface
    {
        return Unit::create($this->unit);
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
        return $this->__toArray();
    }
}
