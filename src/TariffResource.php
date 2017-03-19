<?php

namespace hiqdev\php\billing;

use DateTime;
use hiqdev\php\billing\Object;
use hiqdev\php\billing\Tariff;
use hiqdev\php\billing\Type;
use hiqdev\php\billing\Unit;

/**
 * Tariff Resource.
 */
class TariffResource
{
    /**
     * @var integer
     */
    protected $id;

    /**
     * @var Type
     */
    protected $type;

    /**
     * @var Tariff
     */
    protected $tariff;

    /**
     * @var Object
     */
    protected $object;

    /**
     * @var Unit
     */
    protected $unit;

    /**
     * @var double
     */
    protected $quantity;

    /**
     * @var integer
     */
    protected $price;

    /**
     * Calculate action value.
     * @param Action $action
     * @return null|BillResource
     */
    public function calculateAction(Action $action)
    {
        if (!$this->isApplicable($action)) {
            return null;
        }

        return BillResource($action, $quantity, $sum);
    }

}
