<?php declare(strict_types=1);

namespace hiqdev\php\billing\product\Domain\Model\Unit;

use \hiqdev\php\units\UnitInterface as BaseUnitInterface;

interface UnitInterface
{
    public function name(): string;

    public function createExternalUnit(): BaseUnitInterface;

    public function fractionUnit(): FractionUnitInterface;
}
