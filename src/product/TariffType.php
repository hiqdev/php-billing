<?php declare(strict_types=1);

namespace hiqdev\php\billing\product;

use hiqdev\billing\registry\product\Product;

class TariffType
{
    private string $name;

    private Product $product;

    private PriceTypesCollection $prices;

    private array $behaviors = [];

    public function __construct(string $name)
    {
        $this->name = $name;
        $this->prices = new PriceTypesCollection($this);
    }

    public function ofProduct(Product $product): self
    {
        $this->product = $product;

        return $this;
    }

    public function setPricesSuggester(string $suggesterClass): self
    {
        // Validate or store the suggester class
        return $this;
    }

    public function withPrices(): PriceTypesCollection
    {
        return $this->prices;
    }

    public function withBehaviors(): self
    {
        return $this;
    }

    public function attach(BehaviorInterface $behavior): self
    {
        $this->behaviors[] = $behavior;

        return $this;
    }

    public function end(): self
    {
        // Validate the TariffType and lock its state
        return $this;
    }
}
