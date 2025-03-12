<?php declare(strict_types=1);

namespace hiqdev\php\billing\product\quantity;

use hiqdev\php\units\UnitInterface;

class QuantityFormatterFactory
{
    public static function create(
        UnitInterface $unit,
        QuantityFormatterDefinition $definition,
        FractionQuantityData $data,
    ): QuantityFormatterInterface {
        $formatterClass = $definition->formatterClass();

        return new $formatterClass($unit, $definition->getFractionUnit(), $data);
    }
}
