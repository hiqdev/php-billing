<?php declare(strict_types=1);

namespace hiqdev\php\billing\product;

use hiqdev\billing\registry\invoice\InvoiceRepresentationCollection;
use hiqdev\billing\registry\product\Type;
use hiqdev\billing\registry\product\Unit;

class PriceTypeDefinition
{
    private Unit $unit;

    private string $description;

    private string $quantityFormatter;

    private InvoiceRepresentationCollection $invoiceRepresentation;

    public function __construct(private readonly PriceTypesCollection $parent, private readonly Type $type)
    {
        $this->invoiceRepresentation = new InvoiceRepresentationCollection($this);
    }

    public function unit(Unit $unit): self
    {
        $this->unit = $unit;

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

//    public function invoiceRepresentation(string $representationClass): self
//    {
//        $this->invoiceRepresentation = $representationClass;
//
//        return $this;
//    }

    public function end(): PriceTypesCollection
    {
        // Validate the PriceType and lock its state
        return $this->parent;
    }

    public function documentRepresentation(): InvoiceRepresentationCollection
    {
        return $this->invoiceRepresentation;
    }

    public function measuredWith(\hiqdev\billing\registry\measure\RcpTrafCollector $param): self
    {
        return $this;
    }

    public function type(): Type
    {
        return $this->type;
    }
}
