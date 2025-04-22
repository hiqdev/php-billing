<?php declare(strict_types=1);

namespace hiqdev\php\billing\product\behavior;

use hiqdev\php\billing\product\Domain\Model\TariffTypeInterface;

abstract class Behavior implements BehaviorInterface
{
    private TariffTypeInterface $tariffType;

    public function setTariffType(TariffTypeInterface $tariffTypeName): void
    {
        $this->tariffType = $tariffTypeName;
    }

    public function getTariffType(): TariffTypeInterface
    {
        return $this->tariffType;
    }
}
