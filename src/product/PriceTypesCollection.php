<?php declare(strict_types=1);

namespace hiqdev\php\billing\product;

use hiqdev\billing\registry\product\GType;
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
        $priceType = $this->createPriceTypeDefinition(GType::monthly, $type);

        $this->addPriceTypeDefinition($type, $priceType);

        return $priceType;
    }

    private function addPriceTypeDefinition(PriceType $type, PriceTypeDefinition $priceTypeDefinition): void
    {
        $this->pricesGroupedByPriceType[$type->name][] = $priceTypeDefinition;
    }

    private function createPriceTypeDefinition(GType $gType, PriceType $type): PriceTypeDefinition
    {
        return new PriceTypeDefinition($this, $type, $gType);
    }

    public function overuse(PriceType $type): PriceTypeDefinition
    {
        $priceType = $this->createPriceTypeDefinition(GType::overuse, $type);

        $this->addPriceTypeDefinition($type, $priceType);

        return $priceType;
    }

    public function end(): TariffType
    {
        return $this->parent;
    }

    public function feature(PriceType $type): PriceTypeDefinition
    {
        $priceType = $this->createPriceTypeDefinition(GType::feature, $type);

        $this->addPriceTypeDefinition($type, $priceType);

        return $priceType;
    }

    public function domain(PriceType $type): PriceTypeDefinition
    {
        $priceType = $this->createPriceTypeDefinition(GType::domain, $type);

        $this->addPriceTypeDefinition($type, $priceType);

        return $priceType;
    }

    public function certificate(PriceType $type): PriceTypeDefinition
    {
        $priceType = $this->createPriceTypeDefinition(GType::certificate, $type);

        $this->addPriceTypeDefinition($type, $priceType);

        return $priceType;
    }

    public function discount(PriceType $type): PriceTypeDefinition
    {
        $priceType = $this->createPriceTypeDefinition(GType::discount, $type);

        $this->addPriceTypeDefinition($type, $priceType);

        return $priceType;
    }
}
