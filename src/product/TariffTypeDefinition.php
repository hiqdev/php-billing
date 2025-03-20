<?php declare(strict_types=1);

namespace hiqdev\php\billing\product;

use hiqdev\php\billing\product\behavior\BehaviorTariffTypeCollection;
use hiqdev\php\billing\product\Domain\Model\TariffTypeInterface;
use hiqdev\php\billing\product\price\PriceTypeDefinitionCollection;
use hiqdev\php\billing\product\price\PriceTypeDefinitionFactory;

class TariffTypeDefinition implements TariffTypeDefinitionInterface
{
    private ?ProductInterface $product = null;

    private PriceTypeDefinitionCollection $prices;

    private BehaviorTariffTypeCollection $behaviorCollection;

    public function __construct(private readonly TariffTypeInterface $tariffType)
    {
        $this->prices = new PriceTypeDefinitionCollection($this, new PriceTypeDefinitionFactory());
        $this->behaviorCollection = new BehaviorTariffTypeCollection($this, $tariffType);
    }

    public function tariffType(): TariffTypeInterface
    {
        return $this->tariffType;
    }

    public function ofProduct(ProductInterface $product): TariffTypeDefinitionInterface
    {
        $this->product = $product;

        return $this;
    }

    public function getProduct(): ProductInterface
    {
        if ($this->product === null) {
            throw new ProductNotDefinedException('Product is not set. Call the ofProduct() method first.');
        }

        return $this->product;
    }

    public function setPricesSuggester(string $suggesterClass): TariffTypeDefinitionInterface
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

    public function end(): TariffTypeDefinitionInterface
    {
        // Validate the TariffType and lock its state
        return $this;
    }
}
