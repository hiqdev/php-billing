<?php declare(strict_types=1);

namespace hiqdev\php\billing\product\price;

use hiqdev\php\billing\product\TariffTypeDefinitionInterface;
use hiqdev\php\billing\product\trait\HasLockInterface;
use hiqdev\php\billing\type\TypeInterface;

interface PriceTypeDefinitionCollectionInterface extends \IteratorAggregate, \Countable, HasLockInterface
{
    /**
     * @return PriceTypeDefinitionInterface[]
     */
    public function getIterator(): \Traversable;

    public function priceType(TypeInterface $type): PriceTypeDefinitionInterface;

    public function end(): TariffTypeDefinitionInterface;
}