<?php declare(strict_types=1);

namespace hiqdev\php\billing\price;

final readonly class Sums implements \JsonSerializable
{
    /**
     * @param array<int, float|int>|null $quantityToSumMap An associative array where:
     *   - The key represents the **quantity** of the action being charged for
     *     (e.g., the number of years for an SSL certificate).
     *   - The value represents the **total sum** or **price** for the given quantity.
     *
     * Example (If used to denote bulk prices):
     * E.g. when you buy an SSL certificate for 1 year – it costs 10$
     * But for 2 years you pay 15$.
     *
     * It will be is stored as
     *
     * [1 => 10, 2 => 15]
     */
    public function __construct(private ?array $quantityToSumMap)
    {
        $this->validateSums($this->quantityToSumMap);
    }

    private function validateSums(?array $sums): void
    {
        if (!empty($sums)) {
            foreach ($sums as $value) {
                if (!is_numeric($value)) {
                    throw new PriceInvalidArgumentException('All sums must be numeric values.');
                }
            }
        }
    }

    public function values(): ?array
    {
        return $this->quantityToSumMap;
    }

    public function getSum(int $quantity)
    {
        return $this->quantityToSumMap[$quantity] ?? null;
    }

    public function getMinSum()
    {
        if (empty($this->quantityToSumMap)) {
            return null;
        }

        return min($this->quantityToSumMap);
    }

    public function jsonSerialize(): ?array
    {
        return $this->quantityToSumMap;
    }
}
