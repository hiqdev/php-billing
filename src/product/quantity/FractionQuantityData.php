<?php declare(strict_types=1);

namespace hiqdev\php\billing\product\quantity;

use hiqdev\php\units\Quantity;

final readonly class FractionQuantityData
{
    public function __construct(
        public Quantity $quantity,
        public string $time,
        public ?float $fractionOfMonth
    ) {}
}
