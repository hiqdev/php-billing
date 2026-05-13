<?php declare(strict_types=1);

namespace hiqdev\php\billing\product\behavior;

use hiqdev\php\billing\product\Domain\Model\TariffTypeInterface;
use hiqdev\php\billing\product\price\PriceTypeDefinitionInterface;

/**
 * @template TPriceDefinition
 * @extends BehaviorCollection<TPriceDefinition>
 * @psalm-consistent-templates
 * @psalm-suppress InvalidTemplateParam
 */
class BehaviorPriceTypeDefinitionCollection extends BehaviorCollection
{
    /**
     * @psalm-param TPriceDefinition $parent
     */
    public function __construct(
        /**
         * @psalm-var TPriceDefinition
         */
        private readonly PriceTypeDefinitionInterface $parent,
        TariffTypeInterface $tariffType
    ) {
        parent::__construct($tariffType);
    }

    /**
     * @return TPriceDefinition
     */
    public function end()
    {
        return $this->parent;
    }
}
