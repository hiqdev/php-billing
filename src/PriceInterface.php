<?php

namespace hiqdev\php\billing;

class PriceInterface
{
    /**
     * Calculates charge for given action.
     * @param ActionInterface $action
     * @return ChargeInterface
     */
    public function calculateCharge(ActionInterface $action);
}
