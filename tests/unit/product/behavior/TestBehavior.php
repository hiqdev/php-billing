<?php declare(strict_types=1);

namespace hiqdev\php\billing\tests\unit\product\behavior;

use hiqdev\php\billing\product\behavior\Behavior;

class TestBehavior extends Behavior
{
    private $context;

    public function __construct($context)
    {
        $this->context = $context;
    }

    public function getContext()
    {
        return $this->context;
    }
}
