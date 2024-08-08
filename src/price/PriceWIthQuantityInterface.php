<?php declare(strict_types=1);

namespace hiqdev\php\billing\price;

use hiqdev\php\units\QuantityInterface;

interface PriceWIthQuantityInterface
{
    public function getPrepaid(): QuantityInterface;
}
