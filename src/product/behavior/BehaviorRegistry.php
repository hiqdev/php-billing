<?php

declare(strict_types=1);

namespace hiqdev\php\billing\product\behavior;

use hiqdev\php\billing\product\trait\HasLockInterface;

abstract class BehaviorRegistry implements HasLockInterface, HasBehaviorsInterface
{
    abstract protected function getBehaviorCollection(): BehaviorCollectionInterface;

    public function hasBehavior(string $behaviorClassName): bool
    {
        foreach ($this->getBehaviorCollection() as $behavior) {
            if ($behavior instanceof $behaviorClassName) {
                return true;
            }
        }

        return false;
    }

    public function findBehaviorByClass(string $class)
    {
        foreach ($this->withBehaviors() as $behavior) {
            if ($behavior instanceof $class) {
                return $behavior;
            }
        }

        return null;
    }

    public function lock(): void
    {
        $this->getBehaviorCollection()->lock();
    }
}
