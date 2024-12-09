<?php declare(strict_types=1);

namespace hiqdev\php\billing\product;

use hiqdev\billing\registry\product\Type;

class PriceTypesCollection
{
    private array $prices = [];

    public function __construct(private readonly TariffType $parent)
    {
    }

    public function monthly(Type $type): PriceTypeDefinition
    {
        $priceType = $this->createPriceTypeDefinition($type);
        $this->prices[$type->toTypeName()] = $priceType;

        return $priceType;
    }

    private function createPriceTypeDefinition(Type $type): PriceTypeDefinition
    {
        return new PriceTypeDefinition($this, $type);
    }

    public function overuse(Type $type): PriceTypeDefinition
    {
        $priceType = $this->createPriceTypeDefinition($type);
        $this->prices[$type->toTypeName()] = $priceType;

        return $priceType;
    }

    public function end(): TariffType
    {
        return $this->parent;
    }
}
