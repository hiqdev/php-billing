<?php declare(strict_types=1);

namespace hiqdev\php\billing\product\quantity;

use hiqdev\php\units\UnitInterface;

/**
 * Was created to replace {@see \hipanel\modules\finance\logic\bill\QuantityFormatterFactory}
 */
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
