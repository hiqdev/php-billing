<?php declare(strict_types=1);

namespace hiqdev\php\billing\product;

use hiqdev\billing\registry\Domain\Model\TariffType;
use hiqdev\billing\registry\product\GType;
use hiqdev\billing\registry\product\PriceType;
use hiqdev\billing\registry\product\PriceTypeDefinition\PriceTypeDefinitionFactory;

class PriceTypeDefinitionCollection implements \IteratorAggregate
{
    private array $pricesGroupedByPriceType = [];

    public function __construct(private readonly TariffTypeDefinition $parent)
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
        $priceType = $this->createPriceTypeDefinition(GType::monthly, $type, $this->parent->tariffType());

        $this->addPriceTypeDefinition($type, $priceType);

        return $priceType;
    }

    private function addPriceTypeDefinition(PriceType $type, PriceTypeDefinition $priceTypeDefinition): void
    {
        $this->pricesGroupedByPriceType[$type->name][] = $priceTypeDefinition;
    }

    private function createPriceTypeDefinition(
        GType $gType,
        PriceType $type,
        TariffType $tariffType,
    ): PriceTypeDefinition {
        return PriceTypeDefinitionFactory::create($this, $type, $gType, $tariffType);
    }

    public function overuse(PriceType $type): PriceTypeDefinition
    {
        $priceType = $this->createPriceTypeDefinition(GType::overuse, $type, $this->parent->tariffType());

        $this->addPriceTypeDefinition($type, $priceType);

        return $priceType;
    }

    public function end(): TariffTypeDefinition
    {
        return $this->parent;
    }

    public function feature(PriceType $type): PriceTypeDefinition
    {
        $priceType = $this->createPriceTypeDefinition(GType::feature, $type, $this->parent->tariffType());

        $this->addPriceTypeDefinition($type, $priceType);

        return $priceType;
    }

    public function domain(PriceType $type): PriceTypeDefinition
    {
        $priceType = $this->createPriceTypeDefinition(GType::domain, $type, $this->parent->tariffType());

        $this->addPriceTypeDefinition($type, $priceType);

        return $priceType;
    }

    public function certificate(PriceType $type): PriceTypeDefinition
    {
        $priceType = $this->createPriceTypeDefinition(GType::certificate, $type, $this->parent->tariffType());

        $this->addPriceTypeDefinition($type, $priceType);

        return $priceType;
    }

    public function discount(PriceType $type): PriceTypeDefinition
    {
        $priceType = $this->createPriceTypeDefinition(GType::discount, $type, $this->parent->tariffType());

        $this->addPriceTypeDefinition($type, $priceType);

        return $priceType;
    }
}
