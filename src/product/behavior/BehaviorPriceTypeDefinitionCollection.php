<?php declare(strict_types=1);

namespace hiqdev\php\billing\product\behavior;

use hiqdev\php\billing\product\Domain\Model\TariffTypeInterface;
use hiqdev\php\billing\product\price\PriceTypeDefinition;

class BehaviorPriceTypeDefinitionCollection extends BehaviorCollection
{
    public function __construct(private readonly PriceTypeDefinition $parent, TariffTypeInterface $tariffType)
    {
        parent::__construct($tariffType);
    }

    public function end(): PriceTypeDefinition
    {
        return $this->parent;
    }
}
