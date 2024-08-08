<?php declare(strict_types=1);

namespace hiqdev\php\billing\price;

use hiqdev\php\units\UnitInterface;

interface PriceWithUnitInterface
{
    public function getUnit(): UnitInterface;
}
