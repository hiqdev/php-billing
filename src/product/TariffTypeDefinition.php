<?php declare(strict_types=1);

namespace hiqdev\php\billing\product;

use hiqdev\php\billing\product\behavior\BehaviorTariffTypeCollection;
use hiqdev\php\billing\product\Domain\Model\TariffTypeInterface;
use hiqdev\php\billing\product\price\PriceTypeDefinitionCollection;

class TariffTypeDefinition implements ParentNodeDefinitionInterface
{
    private ProductInterface $product;

    private PriceTypeDefinitionCollection $prices;

    private BehaviorTariffTypeCollection $behaviorCollection;

    public function __construct(private readonly TariffTypeInterface $tariffType)
    {
        $this->prices = new PriceTypeDefinitionCollection($this);
        $this->behaviorCollection = new BehaviorTariffTypeCollection($this, $tariffType);
    }

    public function tariffType(): TariffTypeInterface
    {
        return $this->tariffType;
    }

    public function ofProduct(ProductInterface $product): self
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

    public function withBehaviors(): BehaviorTariffTypeCollection
    {
        return $this->behaviorCollection;
    }

    public function hasBehavior(string $behaviorClassName): bool
    {
        foreach ($this->behaviorCollection as $behavior) {
            if ($behavior instanceof $behaviorClassName) {
                return true;
            }
        }

        return false;
    }

    public function end(): self
    {
        // Validate the TariffType and lock its state
        return $this;
    }
}
