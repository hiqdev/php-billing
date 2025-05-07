<?php declare(strict_types=1);

namespace hiqdev\php\billing\product\price;

use hiqdev\billing\registry\Type\TypeSemantics;
use hiqdev\php\billing\product\AggregateInterface;
use hiqdev\php\billing\product\Exception\AggregateNotDefinedException;
use hiqdev\php\billing\product\behavior\BehaviorPriceTypeDefinitionCollection;
use hiqdev\php\billing\product\invoice\RepresentationCollection;
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

/**
 * @template T of PriceTypeDefinitionCollectionInterface
 * @psalm-consistent-templates
 */
class PriceTypeDefinition implements PriceTypeDefinitionInterface
{
    use HasLock;

    private UnitInterface $unit;

    private string $description;

    private QuantityFormatterDefinition $quantityFormatterDefinition;

    private RepresentationCollection $representationCollection;

    private BehaviorPriceTypeDefinitionCollection $behaviorCollection;

    private ?AggregateInterface $aggregate = null;

    public function __construct(
        /**
         * @psalm-var T
         */
        private readonly PriceTypeDefinitionCollectionInterface $parent,
        private readonly TypeInterface $type,
        TariffTypeInterface $tariffType,
    ) {
        $this->representationCollection = new RepresentationCollection($this);
        $this->behaviorCollection = new BehaviorPriceTypeDefinitionCollection($this, $tariffType);

        $this->init();
    }

    protected function init(): void
    {
        // Hook
    }

    public function unit(UnitInterface $unit): self
    {
        $this->ensureNotLocked();

        $this->unit = $unit;

        return $this;
    }

    public function description(string $description): self
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
     * @param string $formatterClass
     * @param null|FractionUnitInterface|string $fractionUnit
     * @return $this
     * @throws InvalidQuantityFormatterException
     */
    public function quantityFormatter(string $formatterClass, $fractionUnit = null): self
    {
        $this->ensureNotLocked();

        if (!\class_exists($formatterClass)) {
            throw new InvalidQuantityFormatterException("Formatter class $formatterClass does not exist");
        }

        $this->quantityFormatterDefinition = new QuantityFormatterDefinition($formatterClass, $fractionUnit);

        return $this;
    }

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
     * @psalm-return T
     */
    public function end(): PriceTypeDefinitionCollectionInterface
    {
        // Validate the PriceType
        return $this->parent;
    }

    /**
     * @psalm-return RepresentationCollection<self>
     */
    public function documentRepresentation(): RepresentationCollection
    {
        $this->ensureNotLocked();

        return $this->representationCollection;
    }

//    public function measuredWith(\hiqdev\billing\registry\measure\RcpTrafCollector $param): self
//    {
//        $this->ensureNotLocked();
//
//        return $this;
//    }

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

    public function withBehaviors(): BehaviorPriceTypeDefinitionCollection
    {
        $this->ensureNotLocked();

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

    /**
     * @inerhitDoc
     */
    public function aggregation(AggregateInterface $aggregate): self
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

    protected function afterLock(): void
    {
        $this->representationCollection->lock();
        $this->behaviorCollection->lock();
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
