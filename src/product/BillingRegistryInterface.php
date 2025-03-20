<?php declare(strict_types=1);

namespace hiqdev\php\billing\product;

use Generator;
use hiqdev\php\billing\product\price\PriceTypeDefinition;

interface BillingRegistryInterface
{
    /**
     * @return Generator<PriceTypeDefinition>
     */
    public function priceTypes(): Generator;
}