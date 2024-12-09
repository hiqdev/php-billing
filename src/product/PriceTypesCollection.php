<?php declare(strict_types=1);

namespace hiqdev\php\billing\product;

class PriceTypesCollection
{
    private array $prices = [];

    public function __construct(private readonly TariffType $parent)
    {
    }

    public function monthly(string $type): PriceTypeDefinition
    {
        $priceType = $this->createPriceTypeDefinition();
        $this->prices[$type] = $priceType;

        return $priceType;
    }

    private function createPriceTypeDefinition(): PriceTypeDefinition
    {
        return new PriceTypeDefinition($this);
    }

    public function overuse(string $type): PriceTypeDefinition
    {
        $priceType = $this->createPriceTypeDefinition();
        $this->prices[$type] = $priceType;

        return $priceType;
    }

    public function end(): TariffType
    {
        return $this->parent;
    }
}
