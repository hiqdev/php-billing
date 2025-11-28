<?php declare(strict_types=1);

namespace hiqdev\php\billing\product\quantity;

use hiqdev\php\billing\product\Domain\Model\Unit\FractionUnitInterface;

class QuantityFormatterDefinition
{
    /** @var class-string<QuantityFormatterInterface> */
    private string $formatterClass;

    /** @var FractionUnitInterface|null|string */
    private $fractionUnit;

    /**
     * @param class-string<QuantityFormatterInterface> $formatterClass
     * @param FractionUnitInterface|string|null $fractionUnit
     */
    public function __construct(string $formatterClass, $fractionUnit = null)
    {
        $this->formatterClass = $formatterClass;
        $this->fractionUnit = $fractionUnit;
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
