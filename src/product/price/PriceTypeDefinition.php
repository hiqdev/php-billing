<?php declare(strict_types=1);

namespace hiqdev\php\billing\product\price;

use hiqdev\php\billing\product\AggregateInterface;
use hiqdev\php\billing\product\AggregateNotDefinedException;
use hiqdev\php\billing\product\behavior\BehaviorPriceTypeDefinitionCollection;
use hiqdev\php\billing\product\invoice\InvoiceRepresentationCollection;
use hiqdev\php\billing\product\ParentNodeDefinitionInterface;
use hiqdev\php\billing\product\quantity\InvalidQuantityFormatterException;
use hiqdev\php\billing\product\quantity\QuantityFormatterDefinition;
use hiqdev\php\billing\product\quantity\QuantityFormatterFactory;
use hiqdev\php\billing\product\quantity\FractionQuantityData;
use hiqdev\php\billing\product\Domain\Model\TariffTypeInterface;
use hiqdev\php\billing\product\Domain\Model\Unit\FractionUnitInterface;
use hiqdev\php\billing\product\Domain\Model\Unit\UnitInterface;
use hiqdev\php\billing\product\quantity\QuantityFormatterInterface;
use hiqdev\php\billing\type\TypeInterface;

/**
 * @template T of PriceTypeDefinitionCollectionInterface
 * @psalm-consistent-templates
 */
class PriceTypeDefinition implements ParentNodeDefinitionInterface
{
    private UnitInterface $unit;

    private string $description;

    private QuantityFormatterDefinition $quantityFormatterDefinition;

    private InvoiceRepresentationCollection $invoiceCollection;

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
        $this->invoiceCollection = new InvoiceRepresentationCollection($this);
        $this->behaviorCollection = new BehaviorPriceTypeDefinitionCollection($this, $tariffType);

        $this->init();
    }

    protected function init(): void
    {
        // Hook
    }

    public function unit(UnitInterface $unit): self
    {
        $this->unit = $unit;

        return $this;
    }

    public function description(string $description): self
    {
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
        if (!\class_exists($formatterClass)) {
            throw new InvalidQuantityFormatterException("Formatter class $formatterClass does not exist");
        }

        $this->quantityFormatterDefinition = new QuantityFormatterDefinition($formatterClass, $fractionUnit);

        return $this;
    }

    public function createQuantityFormatter(
        FractionQuantityData $data,
    ): QuantityFormatterInterface {
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
        // Validate the PriceType and lock its state
        return $this->parent;
    }

    /**
     * @psalm-return InvoiceRepresentationCollection<self>
     */
    public function documentRepresentation(): InvoiceRepresentationCollection
    {
        return $this->invoiceCollection;
    }

    public function measuredWith(\hiqdev\billing\registry\measure\RcpTrafCollector $param): self
    {
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

    public function withBehaviors(): BehaviorPriceTypeDefinitionCollection
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

    /**
     * це параметер визначає агрегатну функцію яка застосовується для щоденно записаних ресурсів щоб визнизначти
     * місячне споживання за яке потрібно пробілити клієнта
     *
     * @param AggregateInterface $aggregate
     * @return self
     */
    public function aggregation(AggregateInterface $aggregate): self
    {
        $this->aggregate = $aggregate;

        return $this;
    }

    public function getAggregate(): AggregateInterface
    {
        return $this->aggregate;
    }
}
