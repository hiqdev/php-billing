<?php declare(strict_types=1);

namespace hiqdev\php\billing\product\price;

use hiqdev\php\billing\product\TariffTypeDefinitionInterface;
use hiqdev\php\billing\type\TypeInterface;

/**
 * @template T of PriceTypeDefinitionCollectionInterface
 * @template M of TariffTypeDefinitionInterface
 * @mixin T
 */
class PriceTypeDefinitionCollection implements PriceTypeDefinitionCollectionInterface
{
    private PriceTypeStorage $storage;

    public function __construct(
        private readonly TariffTypeDefinitionInterface $parent,
        private readonly PriceTypeDefinitionFactoryInterface $factory,
    ) {
        $this->storage = new PriceTypeStorage();
    }

    /**
     * @return PriceTypeDefinition[]
     */
    public function getIterator(): \Traversable
    {
        return new \ArrayIterator($this->storage->getAll());
    }

    public function priceType(TypeInterface $type): PriceTypeDefinition
    {
        $priceType = $this->factory->create($this, $type, $this->parent->tariffType());
        $this->storage->add($type, $priceType);

        return $priceType;
    }

    /**
     * @return TariffTypeDefinitionInterface
     * @plsam-return M
     */
    public function end(): TariffTypeDefinitionInterface
    {
        return $this->parent;
    }
}
