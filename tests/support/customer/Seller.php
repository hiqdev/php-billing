<?php declare(strict_types=1);

namespace hiqdev\php\billing\tests\support\customer;

use hiqdev\php\billing\customer\Customer;

class Seller extends Customer
{
    public function __construct()
    {
        parent::__construct(1, 'seller');
    }
}
