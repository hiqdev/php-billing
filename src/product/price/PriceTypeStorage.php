<?php declare(strict_types=1);

namespace hiqdev\php\billing\product\price;

class PriceTypeStorage
{
    private array $pricesGroupedByPriceType = [];

    public function add(PriceTypeInterface $type, PriceTypeDefinition $priceTypeDefinition): void
    {
        $this->pricesGroupedByPriceType[$type->name()][] = $priceTypeDefinition;
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
