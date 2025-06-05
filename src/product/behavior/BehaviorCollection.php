<?php declare(strict_types=1);

namespace hiqdev\php\billing\product\behavior;

use hiqdev\php\billing\product\Domain\Model\TariffTypeInterface;
use hiqdev\php\billing\product\price\PriceTypeDefinitionInterface;
use hiqdev\php\billing\product\TariffTypeDefinitionInterface;
use hiqdev\php\billing\product\trait\HasLock;

/**
 * @template-covariant TParentContext of TariffTypeDefinitionInterface|PriceTypeDefinitionInterface
 * @implements BehaviorCollectionInterface<TParentContext>
 * @psalm-consistent-templates
 */
abstract class BehaviorCollection implements BehaviorCollectionInterface
{
    use HasLock;

    /** @var BehaviorInterface[] */
    private array $behaviors = [];

    public function __construct(private readonly TariffTypeInterface $tariffType)
    {
    }

    public function getIterator(): \Traversable
    {
        return new \ArrayIterator($this->behaviors);
    }

    public function attach(BehaviorInterface $behavior): static
    {
        $this->ensureNotLocked();

        $behavior->setTariffType($this->tariffType);

        $this->behaviors[] = $behavior;

        return $this;
    }
}
