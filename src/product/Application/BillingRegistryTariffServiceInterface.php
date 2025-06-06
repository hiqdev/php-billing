<?php declare(strict_types=1);

namespace hiqdev\php\billing\product\Application;

use hiqdev\php\billing\product\Exception\TariffTypeDefinitionNotFoundException;
use hiqdev\php\billing\product\TariffTypeDefinitionInterface;

interface BillingRegistryTariffServiceInterface
{
    /**
     * @param string $tariffName
     * @return TariffTypeDefinitionInterface
     * @throws TariffTypeDefinitionNotFoundException
     */
    public function getTariffTypeDefinitionByName(string $tariffName): TariffTypeDefinitionInterface;
}
