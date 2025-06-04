<?php declare(strict_types=1);

namespace hiqdev\php\billing\product\behavior;

use hiqdev\php\billing\product\price\PriceTypeDefinitionInterface;
use hiqdev\php\billing\product\TariffTypeDefinitionInterface;
use hiqdev\php\billing\product\trait\HasLockInterface;
use IteratorAggregate;
use Traversable;

/**
 * @extends IteratorAggregate<int, BehaviorInterface>
 * @psalm-consistent-templates
 */
interface BehaviorCollectionInterface extends IteratorAggregate, HasLockInterface
{
    /**
     * @return Traversable<int, BehaviorInterface>
     */
    public function getIterator(): Traversable;

    public function attach(BehaviorInterface $behavior): static;

    public function end();
}
