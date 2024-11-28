<?php declare(strict_types=1);

namespace hiqdev\php\billing\product;

class PriceTypeDefinition
{
    private string $unit;

    private string $description;

    private string $quantityFormatter;

    private string $invoiceRepresentation;

    public function unit(string $unit): self
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

    public function invoiceRepresentation(string $representationClass): self
    {
        $this->invoiceRepresentation = $representationClass;

        return $this;
    }

    public function end(): PriceTypesCollection
    {
        // Validate the PriceType and lock its state
        return new PriceTypesCollection();
    }
}
