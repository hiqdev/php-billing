<?php declare(strict_types=1);

namespace hiqdev\php\billing\price;

use hiqdev\php\units\QuantityInterface;

interface PriceWithQuantityInterface extends PriceWithUnitInterface
{
    public function getPrepaid(): QuantityInterface;
}
