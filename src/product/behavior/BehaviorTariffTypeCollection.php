<?php declare(strict_types=1);

namespace hiqdev\php\billing\product\behavior;

use hiqdev\php\billing\product\Domain\Model\TariffTypeInterface;
use hiqdev\php\billing\product\TariffTypeDefinitionInterface;

class BehaviorTariffTypeCollection extends BehaviorCollection
{
    public function __construct(private readonly TariffTypeDefinitionInterface $parent, TariffTypeInterface $tariffType)
    {
        parent::__construct($tariffType);
    }

    public function end(): TariffTypeDefinitionInterface
    {
        $this->lock();

        return $this->parent;
    }
}
