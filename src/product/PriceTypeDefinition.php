<?php declare(strict_types=1);

namespace hiqdev\php\billing\product;

use hiqdev\billing\registry\behavior\BehaviorCollection;
use hiqdev\billing\registry\invoice\InvoiceRepresentationCollection;
use hiqdev\billing\registry\quantity\formatter\QuantityFormatterDefinition;
use hiqdev\billing\registry\quantity\formatter\QuantityFormatterFactory;
use hiqdev\billing\registry\quantity\FractionQuantityData;
use hiqdev\billing\registry\unit\FractionUnit;
use hiqdev\php\billing\quantity\QuantityFormatterInterface;
use hiqdev\php\billing\type\TypeInterface;
use hiqdev\php\units\Unit;
use hiqdev\php\units\UnitInterface;

class PriceTypeDefinition
{
    private UnitInterface $unit;

    private string $description;

    private QuantityFormatterDefinition $quantityFormatterDefinition;

    private InvoiceRepresentationCollection $invoiceCollection;

    private BehaviorCollection $behaviorCollection;

    public function __construct(
        private readonly PriceTypesCollection $parent,
        private readonly TypeInterface $type,
    ) {
        $this->invoiceCollection = new InvoiceRepresentationCollection($this);
        $this->behaviorCollection = new BehaviorCollection($this);

        $this->init();
    }

    protected function init(): void
    {
        // Hook
    }

    public function unit(string $unit): self
    {
        $this->unit = Unit::create($unit);

        return $this;
    }

    public function description(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    /**
     * @param string $formatterClass
     * @param null|FractionUnit|string $fractionUnit
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
            $this->getUnit(),
            $this->quantityFormatterDefinition,
            $data,
        );
    }

    public function end(): PriceTypesCollection
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

    public function withBehaviors(): BehaviorCollection
    {
        return $this->behaviorCollection;
    }
}
