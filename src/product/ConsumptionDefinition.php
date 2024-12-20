<?php declare(strict_types=1);

namespace hiqdev\php\billing\product;

class ConsumptionDefinition
{
    private ConsumptionColumnCollection $columnCollection;

    public function __construct(private readonly TariffType $parent)
    {
        $this->columnCollection = new ConsumptionColumnCollection($this);
    }

    public function columns(): ConsumptionColumnCollection
    {
        return $this->columnCollection;
    }

    public function groups()
    {

    }

    public function end(): TariffType
    {
        return $this->parent;
    }
}
