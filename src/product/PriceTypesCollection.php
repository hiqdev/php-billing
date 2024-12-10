<?php declare(strict_types=1);

namespace hiqdev\php\billing\product;

use hiqdev\billing\registry\product\PriceType;

class PriceTypesCollection implements \IteratorAggregate
{
    private array $pricesGroupedByPriceType = [];

    public function __construct(private readonly TariffType $parent)
    {
    }

    /**
     * @return PriceTypeDefinition[]
     */
    public function getIterator(): \Traversable
    {
        return new \ArrayIterator($this->getAllPrices());
    }

    private function getAllPrices(): array
    {
        $allPrices = [];
        foreach ($this->pricesGroupedByPriceType as $prices) {
            foreach ($prices as $price) {
                $allPrices[] = $price;
            }
        }

        return $allPrices;
    }

    public function monthly(PriceType $type): PriceTypeDefinition
    {
        $priceType = $this->createPriceTypeDefinition($type);

        $this->addPriceTypeDefinition($type, $priceType);

        return $priceType;
    }

    private function addPriceTypeDefinition(PriceType $type, PriceTypeDefinition $priceTypeDefinition): void
    {
        $this->pricesGroupedByPriceType[$type->name][] = $priceTypeDefinition;
    }

    private function createPriceTypeDefinition(PriceType $type): PriceTypeDefinition
    {
        return new PriceTypeDefinition($this, $type);
    }

    public function overuse(PriceType $type): PriceTypeDefinition
    {
        $priceType = $this->createPriceTypeDefinition($type);

        $this->addPriceTypeDefinition($type, $priceType);

        return $priceType;
    }

    public function end(): TariffType
    {
        return $this->parent;
    }
}
