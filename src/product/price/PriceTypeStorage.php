<?php declare(strict_types=1);

namespace hiqdev\php\billing\product\price;

use hiqdev\php\billing\type\TypeInterface;

class PriceTypeStorage
{
    private array $pricesGroupedByPriceType = [];

    public function add(TypeInterface $type, PriceTypeDefinition $priceTypeDefinition): void
    {
        $this->pricesGroupedByPriceType[$type->getName()][] = $priceTypeDefinition;
    }

    /**
     * @return PriceTypeDefinition[]
     */
    public function getAll(): array
    {
        $allPrices = [];
        foreach ($this->pricesGroupedByPriceType as $prices) {
            foreach ($prices as $price) {
                $allPrices[] = $price;
            }
        }

        return $allPrices;
    }
}
