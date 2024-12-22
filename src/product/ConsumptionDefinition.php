<?php declare(strict_types=1);

namespace hiqdev\php\billing\product;

class ConsumptionDefinition
{
    private ConsumptionColumnCollection $columnCollection;
    private ConsumptionGroupCollection $groupCollection;

    public function __construct(private readonly TariffType $parent)
    {
        $this->columnCollection = new ConsumptionColumnCollection($this);
        $this->groupCollection = new ConsumptionGroupCollection($this);
    }

    public function columns(): ConsumptionColumnCollection
    {
        return $this->columnCollection;
    }

    public function groups(): ConsumptionGroupCollection
    {
        return $this->groupCollection;
    }

    public function end(): TariffType
    {
        return $this->parent;
    }
}
