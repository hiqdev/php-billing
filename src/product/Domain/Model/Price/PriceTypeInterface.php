<?php declare(strict_types=1);

namespace hiqdev\php\billing\product\Domain\Model\Price;

interface PriceTypeInterface
{
    public function name(): string;
}