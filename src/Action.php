<?php

namespace hiqdev\php\billing;

use DateTime;
use hiqdev\php\billing\Client;
use hiqdev\php\billing\Object;
use hiqdev\php\billing\Sale;
use hiqdev\php\billing\Type;

/**
 * Billable Action.
 */
class Action
{
    /**
     * @var int
     */
    protected $id;

    /**
     * @var Type main action type
     */
    protected $type;

    /**
     * @var Sale
     */
    protected $sale;

    /**
     * @var Client
     */
    protected $client;

    /**
     * @var Object
     */
    protected $object;

    /**
     * @var double
     */
    protected $amount;

    /**
     * @var DateTime
     */
    protected $time;

    public function __construct(Sale $sale, Object $object, Type $type, double $amount)
    {
        $this->sale = $sale;
        $this->object = $object;
        $this->type = $type;
        $this->amount = $amount;
    }

    public static function createByClient(Client $client, Object $object, Type $type, double $amount)
    {
        $sale = Sale::findByClient($client, $object);

        return new static($sale, $object, $type, $amount);
    }

    /**
     * Returns action matching objects.
     * E.g.:
     * - for server these are all it's hardware parts.
     * - for domain these is it's zone
     * @return void
     */
    public function getObjects()
    {
        if ($this->objects === null) {
            $this->objects = $this->findObjects();
        }

        return $this->objects;
    }

    public function findObjects()
    {
        return [$this->object];
    }
}
