<?php declare(strict_types=1);

namespace hiqdev\php\billing\product\price;

use hiqdev\billing\registry\product\GType;
use hiqdev\billing\registry\product\PriceType;
use hiqdev\billing\registry\product\PriceTypeDefinition\PriceTypeDefinitionFactory;
use hiqdev\php\billing\product\GTypeInterface;
use hiqdev\php\billing\product\TariffTypeDefinition;

class PriceTypeDefinitionCollection implements \IteratorAggregate
{
    private PriceTypeStorage $storage;

    private PriceTypeDefinitionFactory $factory;

    public function __construct(private readonly TariffTypeDefinition $parent)
    {
        $this->storage = new PriceTypeStorage();
        $this->factory = new PriceTypeDefinitionFactory();
    }

    /**
     * @return PriceTypeDefinition[]
     */
    public function getIterator(): \Traversable
    {
        return new \ArrayIterator($this->storage->getAll());
    }

    public function monthly(PriceType $type): PriceTypeDefinition
    {
        return $this->priceType(GType::monthly, $type);
    }

    public function priceType(GTypeInterface $gType, PriceTypeInterface $type): PriceTypeDefinition
    {
        $priceType = $this->factory->create($this, $type, $gType, $this->parent->tariffType());
        $this->storage->add($type, $priceType);

        return $priceType;
    }

    public function overuse(PriceType $type): PriceTypeDefinition
    {
        return $this->priceType(GType::overuse, $type);
    }

    public function feature(PriceType $type): PriceTypeDefinition
    {
        return $this->priceType(GType::feature, $type);
    }

    public function domain(PriceType $type): PriceTypeDefinition
    {
        return $this->priceType(GType::domain, $type);
    }

    public function certificate(PriceType $type): PriceTypeDefinition
    {
        return $this->priceType(GType::certificate, $type);
    }

    public function discount(PriceType $type): PriceTypeDefinition
    {
        return $this->priceType(GType::discount, $type);
    }

    public function end(): TariffTypeDefinition
    {
        return $this->parent;
    }
}
