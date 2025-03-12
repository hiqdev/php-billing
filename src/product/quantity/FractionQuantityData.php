<?php declare(strict_types=1);

namespace hiqdev\php\billing\product\quantity;

use hiqdev\php\units\Quantity;

final class FractionQuantityData
{
    public function __construct(
        public readonly Quantity $quantity,
        public readonly string $time,
        public readonly ?float $fractionOfMonth
    ) {}
}
