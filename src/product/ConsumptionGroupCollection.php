<?php declare(strict_types=1);

namespace hiqdev\php\billing\product;

class ConsumptionGroupCollection implements \IteratorAggregate
{
    /** @var ConsumptionGroup[] */
    private array $groups = [];

    public function __construct(private readonly ConsumptionDefinition $parent)
    {
    }

    /**
     * @return ConsumptionColumn[]
     */
    public function getIterator(): \Traversable
    {
        return new \ArrayIterator($this->groups);
    }

    public function group(): ConsumptionGroup
    {
        $group  = new ConsumptionGroup($this);

        $this->groups[] = $group;

        return $group;
    }

    public function end(): ConsumptionDefinition
    {
        return $this->parent;
    }
}
