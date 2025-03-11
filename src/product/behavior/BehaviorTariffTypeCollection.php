<?php declare(strict_types=1);

namespace hiqdev\php\billing\product\behavior;

use hiqdev\php\billing\product\Domain\Model\TariffTypeInterface;
use hiqdev\php\billing\product\TariffTypeDefinition;

class BehaviorTariffTypeCollection extends BehaviorCollection
{
    public function __construct(private readonly TariffTypeDefinition $parent, TariffTypeInterface $tariffType)
    {
        parent::__construct($tariffType);
    }

    public function end(): TariffTypeDefinition
    {
        return $this->parent;
    }
}
