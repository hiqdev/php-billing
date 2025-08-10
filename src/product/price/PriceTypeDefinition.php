<?php
declare(strict_types=1);

namespace hiqdev\php\billing\product\price;

use hiqdev\php\billing\product\AggregateInterface;
use hiqdev\php\billing\product\behavior\HasBehaviorsInterface;
use hiqdev\php\billing\product\behavior\PriceTypeBehaviorRegistry;
use hiqdev\php\billing\product\Exception\AggregateNotDefinedException;
use hiqdev\php\billing\product\behavior\BehaviorPriceTypeDefinitionCollection;
use hiqdev\php\billing\product\invoice\RepresentationCollection;
use hiqdev\php\billing\product\measure\TrafCollectorInterface;
use hiqdev\php\billing\product\quantity\InvalidQuantityFormatterException;
use hiqdev\php\billing\product\quantity\QuantityFormatterDefinition;
use hiqdev\php\billing\product\quantity\QuantityFormatterFactory;
use hiqdev\php\billing\product\quantity\FractionQuantityData;
use hiqdev\php\billing\product\Domain\Model\TariffTypeInterface;
use hiqdev\php\billing\product\Domain\Model\Unit\FractionUnitInterface;
use hiqdev\php\billing\product\Domain\Model\Unit\UnitInterface;
use hiqdev\php\billing\product\quantity\QuantityFormatterInterface;
use hiqdev\php\billing\product\TariffTypeDefinitionInterface;
use hiqdev\php\billing\product\trait\HasLock;
use hiqdev\php\billing\type\TypeInterface;
use function class_exists;

/**
 * @template TParentCollection
 * @implements PriceTypeDefinitionInterface<TParentCollection, PriceTypeDefinition>
 * @implements HasBehaviorsInterface<PriceTypeDefinition, BehaviorPriceTypeDefinitionCollection>
 * @psalm-consistent-templates
 * @psalm-suppress InvalidTemplateParam
 * @psalm-suppress MissingTemplateParam
 */
class PriceTypeDefinition implements PriceTypeDefinitionInterface
{
    use HasLock;

    private UnitInterface $unit;

    private string $description;

    private ?QuantityFormatterDefinition $quantityFormatterDefinition = null;

    /**
     * @var RepresentationCollection<PriceTypeDefinition>
     */
    private RepresentationCollection $representationCollection;

    private ?AggregateInterface $aggregate = null;

    /** @psalm-var TParentCollection */
    private readonly PriceTypeDefinitionCollectionInterface $parent;

    private readonly PriceTypeBehaviorRegistry $behaviorRegistry;

    /**
     * @param TParentCollection $parent
     */
    public function __construct(
        PriceTypeDefinitionCollectionInterface $parent,
        private readonly TypeInterface $type,
        TariffTypeInterface $tariffType,
    ) {
        $this->parent = $parent;
        $this->representationCollection = new RepresentationCollection($this);
        $this->behaviorRegistry = new PriceTypeBehaviorRegistry($this, $tariffType);

        $this->init();
    }

    protected function init(): void
    {
        // Hook
    }

    public function unit(UnitInterface $unit): static
    {
        $this->ensureNotLocked();

        $this->unit = $unit;

        return $this;
    }

    public function description(string $description): static
    {
        $this->ensureNotLocked();

        $this->description = $description;

        return $this;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    /**
     * @param null|FractionUnitInterface|string $fractionUnit
     * @throws InvalidQuantityFormatterException
     */
    public function quantityFormatter(string $formatterClass, $fractionUnit = null): static
    {
        $this->ensureNotLocked();

        if (!class_exists($formatterClass)) {
            throw new InvalidQuantityFormatterException("Formatter class $formatterClass does not exist");
        }

        $this->quantityFormatterDefinition = new QuantityFormatterDefinition($formatterClass, $fractionUnit);

        return $this;
    }

    /**
     * @psalm-suppress PossiblyNullArgument
     */
    public function createQuantityFormatter(FractionQuantityData $data): QuantityFormatterInterface
    {
        $this->ensureNotLocked();

        return QuantityFormatterFactory::create(
            $this->getUnit()->createExternalUnit(),
            $this->quantityFormatterDefinition,
            $data,
        );
    }

    /**
     * @return TParentCollection
     */
    public function end()
    {
        // Validate the PriceType
        return $this->parent;
    }

    /**
     * @return RepresentationCollection<PriceTypeDefinition>
     * @psalm-suppress LessSpecificImplementedReturnType
     */
    public function documentRepresentation()
    {
        $this->ensureNotLocked();

        return $this->representationCollection;
    }

    public function measuredWith(TrafCollectorInterface $collector): static
    {
        $this->ensureNotLocked();

        // Not completed yet, only for implementing the interface purpose

        return $this;
    }

    public function type(): TypeInterface
    {
        return $this->type;
    }

    public function hasType(TypeInterface $type): bool
    {
        return $this->type->equals($type);
    }

    public function getUnit(): UnitInterface
    {
        return $this->unit;
    }

    /**
     * @return BehaviorPriceTypeDefinitionCollection<PriceTypeDefinition>
     */
    public function withBehaviors()
    {
        $this->ensureNotLocked();

        return $this->behaviorRegistry->withBehaviors();
    }

    public function hasBehavior(string $behaviorClassName): bool
    {
        return $this->behaviorRegistry->hasBehavior($behaviorClassName);
    }

    public function findBehaviorByClass(string $class)
    {
        return $this->behaviorRegistry->findBehaviorByClass($class);
    }

    /**
     * @inerhitDoc
     */
    public function aggregation(AggregateInterface $aggregate): static
    {
        $this->ensureNotLocked();

        $this->aggregate = $aggregate;

        return $this;
    }

    /**
     * @return AggregateInterface
     * @throws AggregateNotDefinedException
     */
    public function getAggregate(): AggregateInterface
    {
        if ($this->aggregate === null) {
            throw new AggregateNotDefinedException('Aggregate is not set. Call the aggregation() method first.');
        }

        return $this->aggregate;
    }

    /**
     * @internal
     */
    public function getQuantityFormatterDefinition(): ?QuantityFormatterDefinition
    {
        return $this->quantityFormatterDefinition;
    }

    protected function afterLock(): void
    {
        $this->representationCollection->lock();
        $this->behaviorRegistry->lock();
    }

    public function getTariffTypeDefinition(): TariffTypeDefinitionInterface
    {
        return $this->parent->getTariffTypeDefinition();
    }

    public function belongsToTariffType(string $tariffTypeName): bool
    {
        return $this->getTariffTypeDefinition()->tariffType()->equalsName($tariffTypeName);
    }
}
