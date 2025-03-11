<?php declare(strict_types=1);

namespace hiqdev\php\billing\product;

use hiqdev\php\billing\product\behavior\BehaviourPriceTypeDefinitionCollection;
use hiqdev\billing\registry\invoice\InvoiceRepresentationCollection;
use hiqdev\billing\registry\quantity\formatter\QuantityFormatterDefinition;
use hiqdev\billing\registry\quantity\formatter\QuantityFormatterFactory;
use hiqdev\billing\registry\quantity\FractionQuantityData;
use hiqdev\php\billing\product\Domain\Model\TariffTypeInterface;
use hiqdev\php\billing\product\Domain\Model\Unit\FractionUnitInterface;
use hiqdev\php\billing\product\Domain\Model\Unit\UnitInterface;
use hiqdev\php\billing\quantity\QuantityFormatterInterface;
use hiqdev\php\billing\type\TypeInterface;

class PriceTypeDefinition implements ParentNodeDefinitionInterface
{
    private UnitInterface $unit;

    private string $description;

    private QuantityFormatterDefinition $quantityFormatterDefinition;

    private InvoiceRepresentationCollection $invoiceCollection;

    private BehaviourPriceTypeDefinitionCollection $behaviorCollection;

    private AggregateInterface $aggregate;

    public function __construct(
        private readonly PriceTypeDefinitionCollection $parent,
        private readonly TypeInterface $type,
        TariffTypeInterface $tariffType,
    ) {
        $this->invoiceCollection = new InvoiceRepresentationCollection($this);
        $this->behaviorCollection = new BehaviourPriceTypeDefinitionCollection($this, $tariffType);

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
     */
    public function quantityFormatter(string $formatterClass, $fractionUnit = null): self
    {
        // TODO: check if formatterClass exists
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

    public function end(): PriceTypeDefinitionCollection
    {
        // Validate the PriceType and lock its state
        return $this->parent;
    }

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

    public function withBehaviors(): BehaviourPriceTypeDefinitionCollection
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
