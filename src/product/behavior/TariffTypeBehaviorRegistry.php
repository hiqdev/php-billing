<?php declare(strict_types=1);

namespace hiqdev\php\billing\product\behavior;

use hiqdev\php\billing\product\Domain\Model\TariffTypeInterface;
use hiqdev\php\billing\product\TariffTypeDefinitionInterface;
use hiqdev\php\billing\product\trait\HasLock;
use hiqdev\php\billing\product\trait\HasLockInterface;

/**
 * class TariffTypeBehaviorRegistry
 *
 * This class acts as a registry for tariff behaviors, encapsulating
 * the BehaviorTariffTypeCollection to provide structured access
 * and behavior lookup functionalities.
 *
 *  Purpose:
 *  - Stores a collection of behaviors associated with a TariffType.
 *  - Provides access to the behavior collection via getBehaviors().
 *  - Allows checking for the existence of a specific behavior using hasBehavior().
 *
 *  Why this class was created:
 *  - To avoid code duplication of behavior-related methods in multiple classes.
 *  - To separate concerns by handling behavior-related logic in a dedicated class.
 *  - To improve maintainability and testability of tariff behavior handling.
 *
 * @template-covariant T of TariffTypeDefinitionInterface
 */
final class TariffTypeBehaviorRegistry implements HasLockInterface
{
    /**
     * @var BehaviorTariffTypeCollection<T>
     */
    private BehaviorTariffTypeCollection $behaviorCollection;

    /**
     * @psalm-param T $tariffTypeDefinition
     */
    public function __construct(TariffTypeDefinitionInterface $tariffTypeDefinition, TariffTypeInterface $tariffType)
    {
        $this->behaviorCollection = new BehaviorTariffTypeCollection($tariffTypeDefinition, $tariffType);
    }

    /**
     * @return BehaviorTariffTypeCollection<T>
     */
    public function getBehaviors(): BehaviorTariffTypeCollection
    {
        return $this->behaviorCollection;
    }

    public function hasBehavior(string $behaviorClassName): bool
    {
        foreach ($this->behaviorCollection as $behavior) {
            if ($behavior instanceof $behaviorClassName) {
                return true;
            }
        }

        return false;
    }

    public function findBehaviorByClass(string $class): ?BehaviorInterface
    {
        foreach ($this->getBehaviors() as $behavior) {
            if ($behavior instanceof $class) {
                return $behavior;
            }
        }

        return null;
    }

    public function lock(): void
    {
        $this->behaviorCollection->lock();
    }
}
