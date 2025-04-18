<?php declare(strict_types=1);

namespace hiqdev\php\billing\product\trait;

interface HasLockInterface
{
    public function lock(): void;
}