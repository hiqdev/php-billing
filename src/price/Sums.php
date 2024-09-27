<?php declare(strict_types=1);

namespace hiqdev\php\billing\price;

final readonly class Sums implements \JsonSerializable
{
    /**
     * @param int[]|null $values quantity => total sum for the quantity
     */
    public function __construct(private ?array $values)
    {
        if (!empty($this->values)) {
            $this->validate($this->values);
        }
    }

    private function validate(array $sums): void
    {
        if ($sums) {
            foreach ($sums as $value) {
                if (!is_numeric($value)) {
                    throw new PriceInvalidArgumentException('Invalid value for sums parameter');
                }
            }
        }
    }

    public function values(): ?array
    {
        return $this->values;
    }

    public function getMinSum(): int|string
    {
        return min($this->values);
    }

    public function jsonSerialize(): ?array
    {
        return $this->values;
    }
}
