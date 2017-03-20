<?php

namespace hiqdev\php\billing;

class TariffInterface
{
    /**
     * Calculates charges for given action.
     * @param ActionInterface $action
     * @return ChargeInterface[]
     */
    public function calculateCharges(ActionInterface $action);
}
