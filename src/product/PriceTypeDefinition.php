<?php declare(strict_types=1);

namespace hiqdev\php\billing\product;

use hiqdev\billing\registry\invoice\InvoiceRepresentationCollection;
use hiqdev\billing\registry\product\GType;
use hiqdev\billing\registry\product\PriceType;
use hiqdev\billing\registry\quantity\formatter\QuantityFormatterDefinition;
use hiqdev\billing\registry\unit\FractionUnit;
use hiqdev\php\units\Unit;

class PriceTypeDefinition
{
    private Unit $unit;

    private string $description;

    private QuantityFormatterDefinition $quantityFormatterDefinition;

    private InvoiceRepresentationCollection $invoiceCollection;

    public function __construct(
        private readonly PriceTypesCollection $parent,
        private readonly PriceType $type,
        private readonly GType $gType,
    ) {
        $this->invoiceCollection = new InvoiceRepresentationCollection($this);

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

    public function quantityFormatter(string $formatterClass, ?FractionUnit $unit = null): self
    {
        // TODO: check if formatterClass exists
        $this->quantityFormatterDefinition = new QuantityFormatterDefinition($formatterClass, $unit);

        return $this;
    }

    public function getQuantityFormatterDefinition(): ?QuantityFormatterDefinition
    {
        return $this->quantityFormatterDefinition;
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

    public function type(): PriceType
    {
        return $this->type;
    }

    public function gType(): GType
    {
        return $this->gType;
    }
}
