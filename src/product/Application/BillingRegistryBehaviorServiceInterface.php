<?php

declare(strict_types=1);

namespace hiqdev\php\billing\product\Application;

use Generator;
use hiqdev\php\billing\product\behavior\BehaviorInterface;
use hiqdev\php\billing\product\behavior\BehaviorNotFoundException;
use hiqdev\php\billing\product\behavior\InvalidBehaviorException;
use hiqdev\php\billing\product\price\PriceTypeDefinitionInterface;

interface BillingRegistryBehaviorServiceInterface
{
    /**
     * @param string $type - full type like 'overuse,lb_capacity_unit'
     * @param string $behaviorClassWrapper
     * @return BehaviorInterface
     * @throws BehaviorNotFoundException
     * @throws InvalidBehaviorException
     */
    public function getBehavior(string $type, string $behaviorClassWrapper): BehaviorInterface;


    /**
     * Find all behaviors attached to any TariffType or PriceType by specified Behavior class.
     *
     * @param string $behaviorClassWrapper
     * @return Generator<BehaviorInterface>
     */
    public function getBehaviors(string $behaviorClassWrapper): Generator;

    /**
     * Find all PriceTypeDefinition in registry by specified Behavior class.
     *
     * @param string $behaviorClassWrapper
     * @return Generator<PriceTypeDefinitionInterface>
     */
    public function findPriceTypeDefinitionsByBehavior(string $behaviorClassWrapper): Generator;

}
