<?php declare(strict_types=1);

namespace hiqdev\php\billing\product;

use hiqdev\billing\registry\product\PriceType;

class PriceTypesCollection
{
    private array $prices = [];

    public function __construct(private readonly TariffType $parent)
    {
    }

    public function monthly(PriceType $type): PriceTypeDefinition
    {
        $priceType = $this->createPriceTypeDefinition($type);
        $this->prices[$type->name] = $priceType;

        return $priceType;
    }

    private function createPriceTypeDefinition(PriceType $type): PriceTypeDefinition
    {
        return new PriceTypeDefinition($this, $type);
    }

    public function overuse(PriceType $type): PriceTypeDefinition
    {
        $priceType = $this->createPriceTypeDefinition($type);
        $this->prices[$type->name] = $priceType;

        return $priceType;
    }

    public function end(): TariffType
    {
        return $this->parent;
    }
}
