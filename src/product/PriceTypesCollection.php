<?php declare(strict_types=1);

namespace hiqdev\php\billing\product;

class PriceTypesCollection
{
    private array $prices = [];

    public function monthly(string $type): PriceTypeDefinition
    {
        $priceType = new PriceTypeDefinition();
        $this->prices[$type] = $priceType;

        return $priceType;
    }

    public function overuse(string $type): PriceTypeDefinition
    {
        $priceType = new PriceTypeDefinition();
        $this->prices[$type] = $priceType;

        return $priceType;
    }

    public function end(): TariffType
    {
        // Return to the parent TariffType context
    }
}
