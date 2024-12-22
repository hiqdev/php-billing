<?php declare(strict_types=1);

namespace hiqdev\php\billing\product;

use hiqdev\billing\registry\product\PriceType;

class ConsumptionGroup implements \IteratorAggregate
{
    private array $group;

    public function __construct(
        private readonly ConsumptionGroupCollection $parent,
    )
    {
    }

    /**
     * @return PriceType[]
     */
    public function getIterator(): \Traversable
    {
        return new \ArrayIterator($this->group);
    }

    public function add(PriceType $priceType): self
    {
        $this->group[] = $priceType;

        return $this;
    }

    public function end(): ConsumptionGroupCollection
    {
        return $this->parent;
    }
}
