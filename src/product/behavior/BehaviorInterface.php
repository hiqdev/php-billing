<?php declare(strict_types=1);

namespace hiqdev\php\billing\product\behavior;

use hiqdev\php\billing\product\Domain\Model\TariffTypeInterface;

/**
 * Empty interface for mark product behavior
 */
interface BehaviorInterface
{
    public function setTariffType(TariffTypeInterface $tariffTypeName): void;

    public function getTariffType(): TariffTypeInterface;
}
