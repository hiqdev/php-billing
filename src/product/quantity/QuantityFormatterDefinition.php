<?php declare(strict_types=1);

namespace hiqdev\php\billing\product\quantity;

use hiqdev\php\billing\product\Domain\Model\Unit\FractionUnitInterface;

class QuantityFormatterDefinition
{
    /**
     * @param class-string<QuantityFormatterInterface> $formatterClass
     * @param FractionUnitInterface|string|null $fractionUnit
     */
    public function __construct(
        private readonly string $formatterClass,
        private $fractionUnit = null
    ) {
    }

    /**
     * @return class-string<QuantityFormatterInterface>
     */
    public function formatterClass(): string
    {
        return $this->formatterClass;
    }

    public function getFractionUnit()
    {
        return $this->fractionUnit;
    }
}
