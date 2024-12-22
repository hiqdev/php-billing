<?php declare(strict_types=1);

namespace hiqdev\php\billing\product;

use hiqdev\billing\registry\product\PriceType;

class ConsumptionColumn
{
    private bool $isConvertible = false;

    private bool $isOverMax = false;

    public function __construct(
        private readonly ConsumptionColumnCollection $parent,
        private readonly PriceType $priceType
    )
    {
    }

    public function convertible(): self
    {
        $this->isConvertible = true;

        return $this;
    }

    public function isConvertible(): bool
    {
        return $this->isConvertible;
    }

    public function overMax(): self
    {
        $this->isOverMax = true;

        return $this;
    }

    public function isOverMax(): bool
    {
        return $this->isOverMax;
    }

    public function priceType(): PriceType
    {
        return $this->priceType;
    }

    public function end(): ConsumptionColumnCollection
    {
        return $this->parent;
    }
}
