<?php declare(strict_types=1);

namespace hiqdev\php\billing\product\price;

use hiqdev\php\billing\product\TariffTypeDefinitionInterface;
use hiqdev\php\billing\product\trait\HasLock;
use hiqdev\php\billing\type\TypeInterface;

/**
 * @template T of PriceTypeDefinitionCollectionInterface
 * @template M of TariffTypeDefinitionInterface
 * @mixin T
 */
class PriceTypeDefinitionCollection implements PriceTypeDefinitionCollectionInterface
{
    use HasLock;

    private PriceTypeStorage $storage;

    private PriceTypeDefinitionCollectionInterface $collectionInstance;

    public function __construct(
        private readonly TariffTypeDefinitionInterface $parent,
        private readonly PriceTypeDefinitionFactoryInterface $factory,
        PriceTypeDefinitionCollectionInterface $collectionInstance = null,
    ) {
        $this->storage = new PriceTypeStorage();
        $this->collectionInstance = $collectionInstance ?? $this;
    }

    /**
     * @inerhitDoc
     */
    public function getIterator(): \Traversable
    {
        return new \ArrayIterator($this->storage->getAll());
    }

    public function priceType(TypeInterface $type): PriceTypeDefinitionInterface
    {
        $this->ensureNotLocked();

        $priceType = $this->factory->create($this->collectionInstance, $type, $this->parent->tariffType());
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

    public function count(): int
    {
        return $this->storage->count();
    }
}
