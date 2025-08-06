<?php declare(strict_types=1);

namespace hiqdev\php\billing\product;

use Google\Service\TPU;
use hiqdev\billing\registry\behavior\ResourceDecoratorBehavior;
use hiqdev\php\billing\price\PriceInterface;
use hiqdev\php\billing\product\behavior\BehaviorCollectionInterface;
use hiqdev\php\billing\product\behavior\BehaviorInterface;
use hiqdev\php\billing\product\behavior\BehaviorTariffTypeCollection;
use hiqdev\php\billing\product\behavior\TariffTypeBehaviorRegistry;
use hiqdev\php\billing\product\Domain\Model\TariffTypeInterface;
use hiqdev\php\billing\product\Exception\ProductNotDefinedException;
use hiqdev\php\billing\product\price\PriceTypeDefinition;
use hiqdev\php\billing\product\price\PriceTypeDefinitionCollection;
use hiqdev\php\billing\product\price\PriceTypeDefinitionCollectionInterface;
use hiqdev\php\billing\product\price\PriceTypeDefinitionFactory;
use hiqdev\php\billing\product\price\PriceTypeDefinitionInterface;
use hiqdev\php\billing\product\trait\HasLock;

/**
 * @template TPriceTypeDefinitionCollection of PriceTypeDefinitionCollection
 * @implements TariffTypeDefinitionInterface<TPriceTypeDefinitionCollection>
 * @psalm-suppress InvalidTemplateParam
 */
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

    public function belongToTariffType(TariffTypeInterface $tariffType): bool
    {
        return $this->tariffType->equals($tariffType);
    }

    public function ofProduct(ProductInterface $product): static
    {
        $this->ensureNotLocked();
        $this->product = $product;

        return $this;
    }

    public function getProduct(): ProductInterface
    {
        $this->ensureProductExists();

        /** @psalm-suppress NullableReturnStatement */
        return $this->product;
    }

    private function ensureProductExists(): void
    {
        if ($this->product === null) {
            throw new ProductNotDefinedException('Product is not set. Call the ofProduct() method first.');
        }
    }

    public function setPricesSuggester(string $suggesterClass): static
    {
        $this->ensureNotLocked();

        // Validate or store the suggester class
        return $this;
    }

    /**
     * @psalm-suppress InvalidReturnType
     * @psalm-suppress InvalidReturnStatement
     */
    public function withPrices()
    {
        $this->ensureNotLocked();

        return $this->prices;
    }

    public function findPricesByTypeName(string $typeName): ?array
    {
        $prices = null;
        $this->ensureNotLocked();

        foreach ($this->prices as $price) {
            if ($this->matchesPriceType($price, $typeName)) {
                $prices[] = $price;
            }
        }
        return $prices;
    }

    private function matchesPriceType(PriceTypeDefinitionInterface $price, string $typeName): bool
    {
        return str_ends_with($price->type()->getName(), ",$typeName");
    }

    /**
     * @return BehaviorCollectionInterface<TariffTypeDefinition>
     * @psalm-suppress ImplementedReturnTypeMismatch
     * @psalm-suppress InvalidReturnType
     * @psalm-suppress InvalidReturnStatement
     */
    public function withBehaviors()
    {
        $this->ensureNotLocked();

        return $this->tariffTypeBehaviorRegistry->getBehaviors();
    }

    public function hasBehavior(string $behaviorClassName): bool
    {
        return $this->tariffTypeBehaviorRegistry->hasBehavior($behaviorClassName);
    }

    public function findBehaviorByClass(string $class): ?BehaviorInterface
    {
        $this->ensureNotLocked();

        foreach ($this->tariffTypeBehaviorRegistry->getBehaviors() as $behavior) {
            if ($behavior instanceof $class) {
                return $behavior;
            }
        }

        return null;
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
