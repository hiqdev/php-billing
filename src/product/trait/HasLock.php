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
}
