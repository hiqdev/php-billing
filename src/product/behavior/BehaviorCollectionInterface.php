<?php declare(strict_types=1);

namespace hiqdev\php\billing\product\behavior;

use hiqdev\php\billing\product\price\PriceTypeDefinitionInterface;
use hiqdev\php\billing\product\TariffTypeDefinitionInterface;
use hiqdev\php\billing\product\trait\HasLockInterface;
use IteratorAggregate;
use Traversable;

/**
 * @extends IteratorAggregate<int, BehaviorInterface>
 */
interface BehaviorCollectionInterface extends IteratorAggregate, HasLockInterface
{
    /**
     * @return Traversable<int, BehaviorInterface>
     */
    public function getIterator(): Traversable;

    public function attach(BehaviorInterface $behavior): self;

    /**
     * @return TariffTypeDefinitionInterface|PriceTypeDefinitionInterface
     */
    public function end(): TariffTypeDefinitionInterface|PriceTypeDefinitionInterface;
}
