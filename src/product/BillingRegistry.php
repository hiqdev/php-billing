<?php declare(strict_types=1);

namespace hiqdev\php\billing\product;

class BillingRegistry implements BillingRegistryInterface
{
    private array $tariffTypes = [];
    private bool $locked = false;

    public function addTariffType(TariffType $tariffType): void
    {
        if ($this->locked) {
            throw new \RuntimeException("BillingRegistry is locked and cannot be modified.");
        }

        $this->tariffTypes[] = $tariffType;
    }

    public function lock(): void
    {
        $this->locked = true;
    }

    public function priceTypes(): \Generator
    {
        foreach ($this->tariffTypes as $tariffType) {
            foreach ($tariffType->withPrices() as $price) {
                yield $price;
            }
        }
    }
}
