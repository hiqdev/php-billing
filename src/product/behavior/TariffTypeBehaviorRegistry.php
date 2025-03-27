<?php declare(strict_types=1);

namespace hiqdev\php\billing\product\behavior;

use hiqdev\php\billing\product\Domain\Model\TariffTypeInterface;
use hiqdev\php\billing\product\TariffTypeDefinitionInterface;

final class TariffTypeBehaviorRegistry
{
    private BehaviorTariffTypeCollection $behaviorCollection;

    public function __construct(TariffTypeDefinitionInterface $tariffTypeDefinition, TariffTypeInterface $tariffType)
    {
        $this->behaviorCollection = new BehaviorTariffTypeCollection($tariffTypeDefinition, $tariffType);
    }

    public function withBehaviors(): BehaviorTariffTypeCollection
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
}
