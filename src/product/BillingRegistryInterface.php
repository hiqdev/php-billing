<?php declare(strict_types=1);

namespace hiqdev\php\billing\product;

interface BillingRegistryInterface
{
    /**
     * @return PriceTypeDefinition[]
     */
    public function priceTypes(): \Generator;
}