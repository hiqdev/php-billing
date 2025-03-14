<?php declare(strict_types=1);

namespace hiqdev\php\billing\product;

use hiqdev\php\billing\product\price\PriceTypeDefinition;

interface BillingRegistryInterface
{
    /**
     * @return PriceTypeDefinition[]
     */
    public function priceTypes(): \Generator;
}