<?php

namespace hiqdev\php\billing;

class Order
{
    public $id;

    public $customer;

    public $actions = [];

    function __construct($id, CustomerInterface $customer, $actions)
    {
        $this->id = $id;
        $this->customer = $customer;
        $this->actions = $actions;
    }
}
