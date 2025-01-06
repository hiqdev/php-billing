<?php declare(strict_types=1);

namespace hiqdev\php\billing\product;

use hiqdev\billing\registry\Domain\Model\TariffType;

/**
 * Empty interface for mark product behavior
 */
interface BehaviorInterface
{
    public function setTariffType(TariffType $tariffTypeName): void;

    public function getTariffType(): TariffType;
}
