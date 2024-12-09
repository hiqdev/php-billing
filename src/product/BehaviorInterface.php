<?php declare(strict_types=1);

namespace hiqdev\php\billing\product;

interface BehaviorInterface
{
    public function execute(): void;
}