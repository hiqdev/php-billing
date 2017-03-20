<?php

namespace hiqdev\php\billing;

use DateTime;

/**
 * Billable Action.
 */
class Action implements ActionInterface
{
    /**
     * @var int
     */
    protected $id;

    /**
     * @var SaleInterface
     */
    protected $sale;

    /**
     * @var TypeInterface main action type
     */
    protected $type;

    /**
     * @var TargetInterface
     * Action.target MAY differ from Sale.target e.g. for domain: Action.target=domain Sale.target=class(zone)
     */
    protected $target;

    /**
     * @var QuantityInterface
     */
    protected $amount;

    /**
     * @var DateTime
     */
    protected $time;

    /**
     * @var TargetInterface[]
     */
    protected $entities;

    /**
     * @var Charge[]
     */
    protected $charges;

    public function __construct(
        SaleInterface $sale,
        TargetInterface $target,
        TypeInterface $type,
        QuantityInterface $quantity,
        DateTime $time
    ) {
        $this->sale = $sale;
        $this->target = $target;
        $this->type = $type;
        $this->amount = $amount;
    }

    public static function createByClient(ClientInterface $client, Target $target, Type $type, double $amount)
    {
        $sale = Sale::findByClient($client, $target);

        return new static($sale, $target, $type, $amount);
    }

    /**
     * Returns matching entities. See [[Type::findRelatedTargets()]]
     * @return Target[]
     */
    public function getRelatedTargets()
    {
        if ($this->entities === null) {
            $this->entities = $this->type->findRelatedTargets($this->target);
        }

        return $this->entities;
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
