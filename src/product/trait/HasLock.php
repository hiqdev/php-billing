<?php declare(strict_types=1);

namespace hiqdev\php\billing\product\trait;

use hiqdev\php\billing\product\Exception\LockedException;

trait HasLock
{
    private bool $locked = false;

    public function lock(): void
    {
        // Lock the state to prevent further modifications
        $this->locked = true;

        $this->afterLock();
    }

    protected function ensureNotLocked(): void
    {
        if ($this->isLocked()) {
            throw new LockedException('Modifications are not allowed after the class has been locked.');
        }
    }

    protected function isLocked(): bool
    {
        return $this->locked;
    }

    protected function afterLock(): void
    {
        // Hook
    }

    /**
     * @param HasLockInterface[] $items
     * @return void
     */
    protected function lockItems(array $items): void
    {
        foreach ($items as $item) {
            $item->lock();
        }
    }
}
