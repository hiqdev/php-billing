<?php declare(strict_types=1);

namespace hiqdev\php\billing\product;

interface GTypeInterface
{
    public function name(): string;

    public function equals(GTypeInterface $otherGType): bool;
}
