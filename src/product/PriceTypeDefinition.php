<?php declare(strict_types=1);

namespace hiqdev\php\billing\product;

use hiqdev\billing\registry\invoice\InvoiceRepresentationCollection;
use hiqdev\billing\registry\product\GType;
use hiqdev\billing\registry\product\PriceType;
use hiqdev\php\units\Unit;

class PriceTypeDefinition
{
    private Unit $unit;

    private string $description;

    private string $quantityFormatter;

    private InvoiceRepresentationCollection $invoiceCollection;

    public function __construct(
        private readonly PriceTypesCollection $parent,
        private readonly PriceType $type,
        private readonly GType $gType,
    ) {
        $this->invoiceCollection = new InvoiceRepresentationCollection($this);
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

    public function quantityFormatter(string $formatterClass): self
    {
        $this->quantityFormatter = $formatterClass;

        return $this;
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
