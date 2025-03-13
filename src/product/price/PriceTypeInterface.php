<?php declare(strict_types=1);

namespace hiqdev\php\billing\product\price;

interface PriceTypeInterface
{
    public function name(): string;
}