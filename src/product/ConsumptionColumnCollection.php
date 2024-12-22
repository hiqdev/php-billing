<?php declare(strict_types=1);

namespace hiqdev\php\billing\product;

use hiqdev\billing\registry\product\PriceType;

class ConsumptionColumnCollection implements \IteratorAggregate
{
    /** @var ConsumptionColumn[] */
    private array $columns = [];

    public function __construct(private readonly ConsumptionDefinition $parent)
    {
    }

    /**
     * @return ConsumptionColumn[]
     */
    public function getIterator(): \Traversable
    {
        return new \ArrayIterator($this->columns);
    }

    public function column(PriceType $priceType): ConsumptionColumn
    {
        $column = new ConsumptionColumn($this, $priceType);

        $this->columns[] = $column;

        return $column;
    }

    public function end(): ConsumptionDefinition
    {
        return $this->parent;
    }
}
