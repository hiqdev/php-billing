<?php declare(strict_types=1);

namespace hiqdev\php\billing\product\behavior;

use hiqdev\php\billing\product\Domain\Model\TariffTypeInterface;
use hiqdev\php\billing\product\TariffTypeDefinitionInterface;

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
 */
final class TariffTypeBehaviorRegistry
{
    private BehaviorTariffTypeCollection $behaviors;

    public function __construct(TariffTypeDefinitionInterface $tariffTypeDefinition, TariffTypeInterface $tariffType)
    {
        $this->behaviors = new BehaviorTariffTypeCollection($tariffTypeDefinition, $tariffType);
    }

    public function getBehaviors(): BehaviorTariffTypeCollection
    {
        return $this->behaviors;
    }

    public function hasBehavior(string $behaviorClassName): bool
    {
        foreach ($this->behaviors as $behavior) {
            if ($behavior instanceof $behaviorClassName) {
                return true;
            }
        }

        return false;
    }
}
