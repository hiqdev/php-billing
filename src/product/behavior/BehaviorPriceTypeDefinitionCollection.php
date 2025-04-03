<?php declare(strict_types=1);

namespace hiqdev\php\billing\product\behavior;

use hiqdev\php\billing\product\Domain\Model\TariffTypeInterface;
use hiqdev\php\billing\product\price\PriceTypeDefinitionInterface;

class BehaviorPriceTypeDefinitionCollection extends BehaviorCollection
{
    public function __construct(private readonly PriceTypeDefinitionInterface $parent, TariffTypeInterface $tariffType)
    {
        parent::__construct($tariffType);
    }

    public function end(): PriceTypeDefinitionInterface
    {
        return $this->parent;
    }
}
