<?php declare(strict_types=1);

namespace hiqdev\php\billing\product\behavior;

use hiqdev\php\billing\product\Domain\Model\TariffTypeInterface;
use hiqdev\php\billing\product\TariffTypeDefinitionInterface;

/**
 * @template T
 */
class BehaviorTariffTypeCollection extends BehaviorCollection
{
    /**
     * @psalm-param T $parent
     */
    public function __construct(
        /**
         * @var T
         */
        private readonly TariffTypeDefinitionInterface $parent,
        TariffTypeInterface $tariffType
    ) {
        parent::__construct($tariffType);
    }

    /**
     * @psalm-return T
     */
    public function end(): TariffTypeDefinitionInterface
    {
        return $this->parent;
    }
}
