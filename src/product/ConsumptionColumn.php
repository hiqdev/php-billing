<?php declare(strict_types=1);

namespace hiqdev\php\billing\product;

class ConsumptionColumn
{
    private bool $isConvertible = false;

    private bool $isOverMax = false;

    public function convertible(): self
    {
        $this->isConvertible = true;

        return $this;
    }

    public function isConvertible(): bool
    {
        return $this->isConvertible;
    }

    public function overMax(): self
    {
        $this->isOverMax = true;

        return $this;
    }

    public function isOverMax(): bool
    {
        return $this->isOverMax;
    }
}
