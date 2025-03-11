<?php declare(strict_types=1);

namespace hiqdev\php\billing\product\behavior;

interface BehaviorCollectionInterface extends \IteratorAggregate
{
    /**
     * @return BehaviorInterface[]
     */
    public function getIterator(): \Traversable;

    public function attach(BehaviorInterface $behavior): self;
}
