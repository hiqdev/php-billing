<?php declare(strict_types=1);

namespace hiqdev\php\billing\product\price;

use hiqdev\php\billing\product\TariffTypeDefinitionInterface;
use hiqdev\php\billing\product\trait\HasLock;
use hiqdev\php\billing\type\TypeInterface;

/**
 * @template TTariffTypeDefinition of TariffTypeDefinitionInterface
 * @template TPriceTypeDefinition of PriceTypeDefinitionCollectionInterface
 * @implements PriceTypeDefinitionCollectionInterface<TPriceTypeDefinition>
 * @mixin TPriceTypeDefinition
 * @psalm-suppress TooManyTemplateParams
 * @psalm-suppress InvalidTemplateParam
 */
class PriceTypeDefinitionCollection implements PriceTypeDefinitionCollectionInterface
{
    use HasLock;

    private PriceTypeStorage $storage;

    private PriceTypeDefinitionCollectionInterface $collectionInstance;

    /**
     * @psalm-param TTariffTypeDefinition $parent
     */
    public function __construct(
        /**
         * @psalm-var TTariffTypeDefinition
         */
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

    /**
     * @psalm-suppress InvalidReturnType
     * @psalm-suppress InvalidReturnStatement
     */
    public function priceType(TypeInterface $type): PriceTypeDefinitionInterface
    {
        $this->ensureNotLocked();

        $priceType = $this->factory->create($this->collectionInstance, $type, $this->parent->tariffType());
        $this->storage->add($type, $priceType);

        return $priceType;
    }

    public function end(): TariffTypeDefinitionInterface
    {
        return $this->parent;
    }

    public function count(): int
    {
        return $this->storage->count();
    }

    public function getTariffTypeDefinition(): TariffTypeDefinitionInterface
    {
        return $this->parent;
    }
}
