<?php declare(strict_types=1);

namespace hiqdev\php\billing\product;

use hiqdev\billing\registry\behavior\TariffTypeBehaviorCollection;
use hiqdev\billing\registry\product\Product;
use hiqdev\billing\registry\Domain\Model\TariffType;

class TariffTypeDefinition implements ParentNodeDefinitionInterface
{
    private Product $product;

    private PriceTypeDefinitionCollection $prices;

    private TariffTypeBehaviorCollection $behaviorCollection;

    public function __construct(private readonly TariffType $tariffType)
    {
        $this->prices = new PriceTypeDefinitionCollection($this);
        $this->behaviorCollection = new TariffTypeBehaviorCollection($this, $tariffType);
    }

    public function tariffType(): TariffType
    {
        return $this->tariffType;
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

    public function withPrices(): PriceTypeDefinitionCollection
    {
        return $this->prices;
    }

    public function withBehaviors(): TariffTypeBehaviorCollection
    {
        return $this->behaviorCollection;
    }

    public function end(): self
    {
        // Validate the TariffType and lock its state
        return $this;
    }
}
