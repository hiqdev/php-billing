<?php

namespace hiqdev\php\billing\order;

use hiqdev\php\billing\customer\CustomerInterface;

/**
 * @author Andrii Vasyliev <sol@hiqdev.com>
 */
interface OrderInterface extends \JsonSerializable
{
    public function getId();

    public function getCustomer();

    /**
     * Returns actions.
     * @return ActionInterface[] array: actionKey => action
     */
    public function getActions();
}
