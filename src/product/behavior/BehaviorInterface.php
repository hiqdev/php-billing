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

    /**
     * Returns a description of the behavior formatted with HTML.
     * The description can be either static, or use the object values.
     *
     * It can be later used for UI or documentation purposes.
     * @return string
     */
    public function description(): string;
}
