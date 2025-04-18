<?php declare(strict_types=1);

namespace hiqdev\php\billing\product\quantity;

use hiqdev\php\billing\product\Domain\Model\Unit\FractionUnitInterface;

class QuantityFormatterDefinition
{
    private string $formatterClass;

    /** @var FractionUnitInterface|null|string */
    private $fractionUnit;

    public function __construct(string $formatterClass, $fractionUnit = null)
    {
        $this->formatterClass = $formatterClass;
        $this->fractionUnit = $fractionUnit;
    }

    public function formatterClass(): string
    {
        return $this->formatterClass;
    }

    public function getFractionUnit()
    {
        return $this->fractionUnit;
    }
}
