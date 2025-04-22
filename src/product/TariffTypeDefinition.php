<?php declare(strict_types=1);

namespace hiqdev\php\billing\product;

use hiqdev\php\billing\product\behavior\BehaviorTariffTypeCollection;
use hiqdev\php\billing\product\behavior\TariffTypeBehaviorRegistry;
use hiqdev\php\billing\product\Domain\Model\TariffTypeInterface;
use hiqdev\php\billing\product\Exception\ProductNotDefinedException;
use hiqdev\php\billing\product\price\PriceTypeDefinitionCollection;
use hiqdev\php\billing\product\price\PriceTypeDefinitionFactory;
use hiqdev\php\billing\product\trait\HasLock;

class TariffTypeDefinition implements TariffTypeDefinitionInterface
{
    use HasLock;

    private ?ProductInterface $product = null;

    private PriceTypeDefinitionCollection $prices;

    private TariffTypeBehaviorRegistry $tariffTypeBehaviorRegistry;

    public function __construct(private readonly TariffTypeInterface $tariffType)
    {
        $this->prices = new PriceTypeDefinitionCollection($this, new PriceTypeDefinitionFactory());
        $this->tariffTypeBehaviorRegistry = new TariffTypeBehaviorRegistry($this, $tariffType);
    }

    public function tariffType(): TariffTypeInterface
    {
        return $this->tariffType;
    }

    public function ofProduct(ProductInterface $product): TariffTypeDefinitionInterface
    {
        $this->ensureNotLocked();
        $this->product = $product;

        return $this;
    }

    public function getProduct(): ProductInterface
    {
        $this->ensureProductExists();

        return $this->product;
    }

    private function ensureProductExists(): void
    {
        if ($this->product === null) {
            throw new ProductNotDefinedException('Product is not set. Call the ofProduct() method first.');
        }
    }

    public function setPricesSuggester(string $suggesterClass): TariffTypeDefinitionInterface
    {
        $this->ensureNotLocked();

        // Validate or store the suggester class
        return $this;
    }

    public function withPrices(): PriceTypeDefinitionCollection
    {
        $this->ensureNotLocked();

        return $this->prices;
    }

    public function withBehaviors(): BehaviorTariffTypeCollection
    {
        $this->ensureNotLocked();

        return $this->tariffTypeBehaviorRegistry->getBehaviors();
    }

    public function hasBehavior(string $behaviorClassName): bool
    {
        return $this->tariffTypeBehaviorRegistry->hasBehavior($behaviorClassName);
    }

    public function end(): TariffTypeDefinitionInterface
    {
        $this->ensureProductExists();

        // Validate prices configuration is complete
        if ($this->prices->count() === 0) {
            throw new \LogicException('At least one price type must be defined');
        }

        return $this;
    }

    protected function afterLock(): void
    {
        $this->prices->lock();
        $this->tariffTypeBehaviorRegistry->lock();
    }
}
