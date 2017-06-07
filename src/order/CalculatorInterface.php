<?php

namespace hiqdev\php\billing\order;

/**
 * @author Andrii Vasyliev <sol@hiqdev.com>
 */
interface CalculatorInterface
{
    public function calculateCharges(OrderInterface $order);
}
