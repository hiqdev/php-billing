<?php declare(strict_types=1);

namespace hiqdev\php\billing\tests\support\customer;

use hiqdev\php\billing\customer\Customer;

class Client extends Customer
{
    public function __construct()
    {
        parent::__construct(2, 'client', new Seller());
    }
}
