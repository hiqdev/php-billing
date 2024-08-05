<?php declare(strict_types=1);

namespace hiqdev\php\billing\formula;

class Formula
{
    public function __construct(private ?string $value, private readonly FormulaEngine $formulaEngine)
    {
        if (!empty($this->value)) {
            $this->value = $this->formulaEngine->normalize($this->value);
            $this->validate($this->value);
        }
    }

    private function validate(string $value): void
    {
        $error = $this->formulaEngine->validate($value);
        if ($error !== null) {
            throw new FormulaEngineException($error);
        }
    }

    public function value(): ?string
    {
        return $this->value;
    }
}
