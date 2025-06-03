<?php

declare(strict_types=1);

namespace hiqdev\php\billing\product\Application;

use hiqdev\php\billing\product\behavior\BehaviorInterface;
use hiqdev\php\billing\product\TariffTypeDefinitionInterface;

interface BillingRegistryTariffServiseInterface
{

    public function findTariffTypeDefinitionByBehavior(BehaviorInterface $behavior): TariffTypeDefinitionInterface;

    public function getTariffDefinitionByName(string $tariffName): ?TariffTypeDefinitionInterface;

    public function hasBehaviour(TariffTypeDefinitionInterface $tariffTypeDefinition, string $behaviorClassWrapper): bool;
}
