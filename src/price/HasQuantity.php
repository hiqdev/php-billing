<?php declare(strict_types=1);

namespace hiqdev\php\billing\price;

use hiqdev\php\units\QuantityInterface;
use hiqdev\php\units\UnitInterface;

trait HasQuantity
{
    /**
     * @var QuantityInterface prepaid quantity also implies Unit
     * XXX cannot be null cause Unit is required
     */
    protected QuantityInterface $prepaid;

    public function getPrepaid(): QuantityInterface
    {
        return $this->prepaid;
    }

    public function getUnit(): UnitInterface
    {
        return $this->prepaid->getUnit();
    }
}
