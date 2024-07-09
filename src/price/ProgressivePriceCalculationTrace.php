<?php

declare(strict_types=1);

namespace hiqdev\php\billing\price;

use hiqdev\php\units\Quantity;
use Money\Money;
use Stringable;

/**
 * Class ProgressivePriceCalculationTrace represents a single step in the calculation of a progressive price.
 *
 * @author Dmytro Naumenko <d.naumenko.a@gmail.com>
 */
final class ProgressivePriceCalculationTrace implements Stringable
{
    public function __construct(
        public ProgressivePriceThreshold $threshold,
        public Quantity $billedUsage,
        public Money $charged,
    ) {
    }

    public function __toString(): string
    {
        return sprintf(
            "%s%s * %s = %s",
            $this->billedUsage->getQuantity(),
            $this->billedUsage->getUnit()->getName(),
            $this->threshold->getRawPrice(),
            number_format($this->charged->getAmount()/100, 2),
        );
    }

    public function toShortString(): string
    {
        return sprintf(
            "%s*%s",
            $this->billedUsage->getQuantity(),
            $this->threshold->getRawPrice(),
        );
    }
}
