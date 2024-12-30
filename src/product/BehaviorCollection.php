<?php declare(strict_types=1);

namespace hiqdev\php\billing\product;

use hiqdev\billing\registry\Domain\Model\TariffType;

class BehaviorCollection implements \IteratorAggregate
{
    /** @var BehaviorInterface[] */
    private array $behaviors = [];

    public function __construct(private readonly TariffType $tariffType)
    {
    }

    /**
     * @return BehaviorInterface[]
     */
    public function getIterator(): \Traversable
    {
        return new \ArrayIterator($this->behaviors);
    }

    public function attach(BehaviorInterface $behavior): self
    {
        $behavior->setTariffType($this->tariffType);

        $this->behaviors[] = $behavior;

        return $this;
    }
}
