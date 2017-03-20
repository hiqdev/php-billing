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
     * @var Sale
     */
    protected $sale;

    /**
     * @var Type main action type
     */
    protected $type;

    /**
     * @var Object
     * Action.object MAY differ from Sale.object e.g. for domain: Action.object=domain Sale.object=class(zone)
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

    /**
     * @var Object[]
     */
    protected $objects;

    /**
     * @var Charge[]
     */
    protected $charges;

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
     * Returns matching objects. See [[Type::findMatchingObjects()]]
     * @return Object[]
     */
    public function getObjects()
    {
        if ($this->objects === null) {
            $this->objects = $this->type->findMatchingObjects($this->object);
        }

        return $this->objects;
    }

    /**
     * Returns calculated charges.
     * @return Charge[]
     */
    public function getCharges()
    {
        if ($this->charges === null) {
            $this->charges = $this->sale->getTariff()->calculateCharges($this);
        }

        return $this->charges;
    }
}
