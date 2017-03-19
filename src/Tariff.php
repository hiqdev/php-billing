<?php

namespace hiqdev\php\billing;

use hiqdev\php\billing\Client;
use hiqdev\php\billing\Object;
use hiqdev\php\billing\TariffResource;

/**
 * Tariff Plan.
 */
class Tariff
{
    /**
     * @var int
     */
    public $id;

    /**
     * @var string
     */
    public $name;

    /**
     * @var Tariff|null
     * XXX not sure to implement
     */
    public $parent;

    /**
     * @var Client
     */
    public $client;

    /**
     * @var Object
     */
    public $object;

    /**
     * @var TariffResource[]
     */
    public $resources = [];

    /**
     * Calculate action value.
     * @param Action $action
     * @return BillResource[]
     */
    public function calculateAction(Action $action)
    {
        $results = [];
        foreach ($this->getResources() as $resource) {
            $result = $resource->calculateAction($action);
            if ($result) {
                $results[] = $result;
            }
        }

        return $results;
    }
}
